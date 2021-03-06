<?php

use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Supplier\SupplierController;
use App\Http\Controllers\Api\Users\RoleController;
use App\Http\Controllers\Api\Users\UserController;
use App\Http\Controllers\Api\Price\PurchasePriceController;
use App\Http\Controllers\Api\Price\SellingPriceController;
use App\Http\Controllers\Api\Product\Unit\UnitController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(['middleware' => ['auth:sanctum']], function () {
    Route::get('/me', function(Request $request) {
        return $request->user();
    });

    // Product
    Route::apiResource('product', ProductController::class);

    // Category
    Route::apiResource('category', CategoryController::class);

    // Suppliers
    Route::apiResource('suppliers', SupplierController::class);

    // Units
    Route::apiResource('units', UnitController::class);

    // PurchasePrices
    Route::apiResource('purchase-prices', PurchasePriceController::class);

    // SellingPrices
    Route::apiResource('selling-prices', SellingPriceController::class);

    // Users
    Route::apiResource('users', UserController::class);

    // Roles
    Route::apiResource('roles', RoleController::class);
});
