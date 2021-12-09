<?php

namespace App\Http\Controllers\Api\Price;

use App\Http\Controllers\Controller;
use App\Http\Requests\PurchasePriceRequest;
use App\Models\PurchasePrice;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PurchasePriceController extends Controller
{
    protected $purchasePrices;

    public function __construct(PurchasePrice $purchasePrices)
    {
        $this->purchasePrices = $purchasePrices;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('search')) {
            $this->purchasePrices = $this->purchasePrices->where('name', 'LIKE', '%' . $request->search . '%');
        }

        return response()->json([
            'success' => true,
            'message' => 'purchase price list',
            'data' => $this->purchasePrices->paginate($request->length ?? 5)->appends(['search' => $request->search])
        ], Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(PurchasePriceRequest $request)
    {
        try {
            $this->purchasePrices->create($request->validated());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'purchase price created'
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
            $purchasePrices = $this->purchasePrices->findOrFail($id);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'purchase price show',
            'data' => $purchasePrices
        ], Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(PurchasePriceRequest $request, $id)
    {
        try {
            $this->purchasePrices->findOrFail($id)->update($request->validated());
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'purchase price updated',
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
            $this->purchasePrice->find($id)->delete();
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        return response()->json([
            'success' => true,
            'message' => 'purchase price deleted'
        ], Response::HTTP_OK);
    }
}
