<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\CouponController;
use App\Http\Controllers\Api\CouponShopController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'coupons'], function () {
    Route::get('/', [CouponController::class, 'index']);
    Route::post('/', [CouponController::class, 'store']);
    
    Route::group(['prefix' => '{id}'], function () {
        Route::put('/', [CouponController::class, 'update']);
        Route::delete('/', [CouponController::class, 'destroy']);
        Route::get('/', [CouponController::class, 'show']);
    });
});

Route::group(['prefix' => 'shops'], function () {
    Route::get('/', [ShopController::class, 'index']);
    Route::post('/', [ShopController::class, 'store']);
    
    Route::group(['prefix' => '{id}'], function () {
        Route::put('/', [ShopController::class, 'update']);
        Route::delete('/', [ShopController::class, 'destroy']);
        Route::get('/', [ShopController::class, 'show']);
    });
});

Route::get('/coupons/{coupon_id}/shops', [CouponShopController::class, 'getRelatedShops']);
Route::get('/coupons/{coupon_id}/shops/{shop_id}', [CouponShopController::class, 'getRelatedShop']);
Route::post('/coupons/{coupon_id}/shops', [CouponShopController::class, 'createCouponShop']);
Route::delete('/coupons/{coupon_id}/shops/{id}', [CouponShopController::class, 'deleteCouponShop']);
