<?php

use App\Http\Controllers\Api\Category\CategoryController;
use App\Http\Controllers\Api\Product\ProductController;
use App\Http\Controllers\Api\Supplier\SupplierController;
use App\Http\Controllers\Api\Users\UserController;
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

    // Supplier
    Route::apiResource('supplier', SupplierController::class);

    // Users
    Route::apiResource('users', UserController::class);
});
