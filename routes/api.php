<?php

use App\Http\Controllers\ProductCategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductImageController;
use App\Models\ProductImage;
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

Route::group(['middleware' => ['auth:api']], function () {
    Route::post('/products', [ProductController::class, 'store'])->name('api.products.store');
    Route::put('/products/{product?}', [ProductController::class, 'update'])->name('api.products.update');
    Route::get('/products', [ProductController::class, 'data'])->name('api.products.data');
    Route::get('/products/{product?}', [ProductController::class, 'show'])->name('api.products.show');
    Route::delete('/products/{product?}', [ProductController::class, 'destroy'])->name('api.products.destroy');
    Route::get('/product-categories', [ProductCategoryController::class, 'data'])->name('api.product-categories.data');


    Route::name("api.")->group(function ($router) {
        Route::apiResource('product-images', ProductImageController::class);
    });
});
