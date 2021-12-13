<?php

namespace App\Http\Controllers\Api\Category;

use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryStoreRequest;
use App\Http\Requests\CategoryUpdateRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class CategoryController extends Controller
{
    protected $categories;

    public function __construct(Category $categories)
    {
        $this->categories = $categories;
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

            $this->categories = $this->categories->with($relations);
        }

        if ($request->has('search')) {
            $this->categories = $this->categories->where('name', 'LIKE', '%' . $request->search . '%')
                ->orWhereRelation('parent', 'name', 'LIKE', '%' . $request->search . '%');
        }

        $this->categories = !$request->has('no_paginate')
            ? $this->categories->paginate($request->length ?? self::DEFAULT_PAGE_LENGTH)->appends(['search' => $request->search])
            : $this->categories->get();

        return response()->json([
            'success' => true,
            'data' => $this->categories,
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(CategoryStoreRequest $request, Category $category)
    {
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->created_by = auth()->user()->id;

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully.',
            'data' => $category
        ], Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, Category $category)
    {
        if ($request->has('relations')) {
            $relations = explode(',', $request->relations);

            $category = $category->load($relations);
        }

        return response()->json($category, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CategoryUpdateRequest $request, Category $category)
    {
        $category->name = $request->name;
        $category->parent_id = $request->parent_id;
        $category->created_by = auth()->user()->id;

        $category->save();

        return response()->json([
            'success' => true,
            'message' => 'category updated',
            'data' => $category
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Category $category)
    {
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'category deleted',
        ], Response::HTTP_OK);
    }
}
