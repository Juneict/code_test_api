<?php

namespace App\Http\Controllers\Api;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use App\Models\CouponShop;
use Illuminate\Support\Facades\Validator;

class CouponShopController extends Controller
{
    public function getRelatedShops(Request $request, $couponId)
    {
        $validatedData = $request->validate([
            'limit' => 'integer|nullable',
            'offset' => 'integer|nullable',
        ]);

        $limit = $validatedData['limit'] ?? 30;
        $offset = $validatedData['offset'] ?? 0;

        $coupon = Coupon::with(['shops' => function ($query) use ($limit, $offset) {
                if ($limit !== null) {
                    $query->limit($limit);
                }
                if ($offset !== null) {
                    $query->offset($offset);
                }
            }])
            ->find($couponId);

        if (!$coupon) {
            return response()->json([
                'success' => 0,
                'code' => 404,
                'errors' => [
                    'message' => 'Coupon not found',
                ],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ], 404);
        }

        return response()->json([
            'success' => 1,
            'code' => 200,
            'meta' => [
                'method' => 'GET',
                'endpoint' => "1/coupons/$couponId/shops",
                'limit' => $limit,
                'offset' => $offset,
            ],
            'data' => [
                'coupon' => $coupon,
                'shops' => ShopResource::collection($coupon->shops),
            ],
            'errors' => [],
            'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
        ]);
    }

    public function getRelatedShop(Request $request, $couponId, $shopId)
    {
        $coupon = Coupon::find($couponId);

        if (!$coupon) {
            return response()->json([
                'success' => 0,
                'code' => 404,
                'errors' => [
                    'message' => 'Coupon not found',
                ],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ], 404);
        }

        $shop = $coupon->shops()->find($shopId);

        if (!$shop) {
            return response()->json([
                'success' => 0,
                'code' => 404,
                'errors' => [
                    'message' => 'Shop not found for the given coupon',
                ],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ], 404);
        }

        return response()->json([
            'success' => 1,
            'code' => 200,
            'meta' => [
                'method' => 'GET',
                'endpoint' => "1/coupons/$couponId/shops/$shopId",
            ],
            'data' => [
                'coupon' => $coupon,
                'shop' => new ShopResource($shop),
            ],
            'errors' => [],
            'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
        ]);
    }

    public function createCouponShop(Request $request, $couponId)
    {
        $validator = Validator::make($request->all(), CouponShop::rules());

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'code' => 400,
                'meta' => [
                    'method' => 'POST',
                    'endpoint' => "1/coupons/$couponId/shops",
                ],
                'data' => [],
                'errors' => [
                    'message' => 'The request parameters are incorrect, please make sure to follow the documentation about request parameters of the resource.',
                    'code' => 400002,
                    'validation' => $validator->errors()->toArray(),
                ],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ], 400);
        }

        $shopId = $request['shop_id'];

        $coupon = Coupon::find($couponId);

        if (!$coupon) {
            return response()->json([
                'success' => 0,
                'code' => 404,
                'meta' => [
                    'method' => 'POST',
                    'endpoint' => "1/coupons/$couponId/shops",
                ],
                'data' => [],
                'errors' => [
                    'message' => 'The parent resource corresponding to the given ID was not found.',
                    'code' => 404005,
                ],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ], 404);
        }

        if ($coupon->shops()->where('shop_id', $shopId)->exists()) {
            return response()->json([
                'success' => 0,
                'code' => 409,
                'meta' => [
                    'method' => 'POST',
                    'endpoint' => "1/coupons/$couponId/shops",
                ],
                'data' => [],
                'errors' => [
                    'message' => 'The inserting resource was already registered.',
                    'code' => 409001,
                ],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ], 409);
        }

        $coupon->shops()->attach($shopId);

        $pivotId = $coupon->shops()->wherePivot('shop_id', $shopId)->first()->pivot->id;

        return response()->json([
            'success' => 1,
            'code' => 201,
            'meta' => [
                'method' => 'POST',
                'endpoint' => "1/coupons/$couponId/shops",
            ],
            'data' => [
                'id' => $pivotId,
            ],
            'errors' => [],
            'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
        ], 201);
    }

    public function deleteCouponShop($couponId, $id)
    {
        $coupon = Coupon::find($couponId);

        if (!$coupon) {
            return response()->json([
                'success' => 0,
                'code' => 404,
                'meta' => [
                    'method' => 'DELETE',
                    'endpoint' => "1/coupons/$couponId/shops/$id",
                ],
                'data' => [],
                'errors' => [
                    'message' => 'The parent resource corresponding to the given ID was not found.',
                    'code' => 404005,
                ],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ], 404);
        }

        $result = $coupon->shops()->detach($id);

        if ($result == 0) {
            return response()->json([
                'success' => 0,
                'code' => 404,
                'meta' => [
                    'method' => 'DELETE',
                    'endpoint' => "1/coupons/$couponId/shops/$id",
                ],
                'data' => [],
                'errors' => [
                    'message' => 'The resource corresponding to the given ID was not found.',
                    'code' => 404005,
                ],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ], 404);
        }

        return response()->json([
            'success' => 1,
            'code' => 200,
            'meta' => [
                'method' => 'DELETE',
                'endpoint' => "1/coupons/$couponId/shops/$id",
            ],
            'data' => [
                'message' => 'Shop association with the coupon deleted successfully',
            ],
            'errors' => [],
            'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
        ], 200);
    }
}
