<?php

namespace App\Http\Controllers\Api\Product;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

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

        $products = Product::when(request('search'), function($q) use ($request) {
            $q->where('name', 'LIKE', "%$request->search%");
        })
        ->paginate($pageLength, ['*'], 'page', $pageNumber);

        return response()->json([
            'success' => true,
            'message' => 'product list',
            'data' => $products
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request, Product $product)
    {
        $product->name = $request->name;
        $product->description = $request->description;
        $product->created_by = $request->created_by;

        if ($request->file('image')) {
            // upload image
            $name = $request->file('image')->getClientOriginalName();
            $dir = $request->file('image')->move('product', $name);
            
            $product->image = $name;
        }

        $product->save();

        // attach category
        $product->category()->attach($request->category);

        return response()->json([
            'success' => true,
            'message' => 'product created',
            'data' => $product
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Product $product)
    {
        return response()->json([
            'success' => true,
            'message' => 'product show',
            'data' => $product
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Product $product)
    {
        $product->name = $request->name;
        $product->description = $request->description;
        $product->image = $request->image;
        $product->created_by = $request->created_by;

        $product->save();

        return response()->json([
            'success' => true,
            'message' => 'product updated',
            'data' => $product
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Product $product)
    {
        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'product deleted'
        ], 200);
    }
}
