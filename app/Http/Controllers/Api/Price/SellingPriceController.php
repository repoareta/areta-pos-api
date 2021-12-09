<?php

namespace App\Http\Controllers\Api\Price;

use App\Http\Controllers\Controller;
use App\Http\Requests\SellingPriceRequest;
use App\Models\SellingPrice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class SellingPriceController extends Controller
{
    protected $sellingPrices;

    public function __construct(SellingPrice $sellingPrices)
    {
        $this->sellingPrices = $sellingPrices;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('search')) {
            $this->sellingPrices = $this->sellingPrices->where('name', 'LIKE', '%' . $request->search . '%');
        }

        return response()->json([
            'success' => true,
            'message' => 'selling price list',
            'data' => $this->sellingPrices->paginate($request->length ?? 5)->appends(['search' => $request->search])
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SellingPriceRequest $request)
    {
        try {
            $this->sellingPrices->create($request->validated());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'selling price created'
        ], Response::HTTP_OK);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $sellingPrices = $this->sellingPrices->findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'selling price show',
            'data' => $sellingPrices
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(SellingPriceRequest $request, $id)
    {
        try {
            $this->sellingPrices->findOrFail($id)->update($request->validated());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'selling price updated',
        ], Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $this->sellingPrice->find($id)->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'selling price deleted'
        ], Response::HTTP_OK);
    }
}
