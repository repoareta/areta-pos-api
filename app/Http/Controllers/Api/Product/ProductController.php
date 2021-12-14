<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    protected $products;

    public function __construct(Product $products)
    {
        $this->products = $products;
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('relations')) {
            $relations = explode(',', $request->relations);

            $this->products = $this->products->with($relations);
        }

        if ($request->has('search')) {
            $this->products = $this->products->where('name', 'LIKE', '%' . $request->search . '%')
                ->orWhereRelation('category', 'name', 'LIKE', '%' . $request->search . '%');
        }

        $this->products = !$request->has('no_paginate')
            ? $this->products->paginate($request->length ?? self::DEFAULT_PAGE_LENGTH)->appends(['search' => $request->search])
            : $this->products->get();

        return response()->json([
            'success' => true,
            'message' => 'product list',
            'data' => $this->products,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request)
    {
        try {
            $product = $this->products->create($request->validated());

            $product->category()->attach(
                is_array($request->category)
                ? $request->category
                : explode(',', $request->category)
            );

            if ($request->file('image')) {
                $this->upload($product, $request->file('image'));
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product created successfully.',
            'data' => $product
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Product $product)
    {
        if ($request->has('relations')) {
            $relations = explode(',', $request->relations);

            $product = $product->load($relations);
        }

        return response()->json([
            'success' => true,
            'message' => 'product show',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(ProductUpdateRequest $request, Product $product)
    {
        try {
            $product->update($request->validated());

            $product->category()->sync(
                is_array($request->category)
                ? $request->category
                : explode(',', $request->category)
            );

            if ($request->file('image')) {
                $this->upload($product, $request->file('image'));
            }
        } catch (\Throwable $th) {
            return response()->json([
                'message' => $th->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully.',
            'data' => $product
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->category()->detach();
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully.'
        ], Response::HTTP_OK);
    }

    /**
     * an Event to handle image upload.
     *
     * @param \App\Models\Product $product
     * @param  mixed  $imageFile
     * @return \App\Models\Product
     */
    private function upload(Product $product, $imageFile)
    {
        $uploadedFile = (new FileUploadService($imageFile, "products/$product->id/"))
            ->upload()
            ->resize(300, 300)
            ->getFileName();

        if ($product->image) {
            $product->image()->update([
                'path' => public_path("storage/products/$product->id"),
                'file_name' => $uploadedFile,
                'source' => asset("storage/products/$product->id/$uploadedFile"),
            ]);

            return $product;
        }

        $product->image()->create([
            'path' => public_path("storage/products/$product->id"),
            'file_name' => $uploadedFile,
            'source' => asset("storage/products/$product->id/$uploadedFile"),
        ]);

        return $product;
    }
}
