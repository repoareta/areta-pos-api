<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\ProductStoreRequest;
use App\Http\Requests\ProductUpdateRequest;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pageNumber = $request->page ? $request->page : 1 ; // set default
        $pageLength = $request->length ? $request->length : 5 ; // set default

        $products = Product::with(['category'])->when(request('search'), function($q) use ($request) {
            $q->where('name', 'LIKE', "%$request->search%");
        })
        ->paginate($pageLength, ['*'], 'page', $pageNumber);

        return response()->json([
            'success' => true,
            'message' => 'product list',
            'data' => $products
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(ProductStoreRequest $request, Product $product)
    {
        $product->name = $request->name;
        $product->description = $request->description;
        $product->unit_id = $request->unit_id;
        $product->initial_stock = $request->initial_stock;
        $product->created_by = auth()->user()->id;

        if ($request->file('image')) {
            // upload image
            $name = $request->file('image')->getClientOriginalName();
            $dir = $request->file('image')->move('product', $name);

            $product->image = $name;
        }

        $product->save();

        // attach category
        $product->category()->attach(explode(',', $request->category));

        return response()->json([
            'success' => true,
            'message' => 'product created',
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
        $product->name = $request->name;
        $product->description = $request->description;
        $product->unit_id = $request->unit_id;
        $product->initial_stock = $request->initial_stock;
        $product->created_by = auth()->user()->id;

        $product->save();

        $product->category()->sync(is_array($request->category) ? $request->category : explode(',', $request->category));

        return response()->json([
            'success' => true,
            'message' => 'product updated',
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
            'message' => 'product deleted'
        ], Response::HTTP_OK);
    }
}
