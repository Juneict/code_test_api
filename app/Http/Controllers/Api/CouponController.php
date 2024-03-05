<?php

namespace App\Http\Controllers\Api;

use App\Models\Coupon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\CouponResource;
use Illuminate\Support\Facades\Validator;
use App\ServiceInterfaces\CouponServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class CouponController extends Controller
{
    /** @var $couponService */
    private $couponService;

    public function __construct(
        CouponServiceInterface $couponService
    ) {
        $this->couponService = $couponService;
    }

    /**
     * List : Coupons
     * 
     * @param Request $request
     * 
     * @return mixed
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', config('coupon.limit'));
        $offset = $request->input('offset', config('coupon.offset'));

        $coupons = $this->couponService->getAllCoupons($request->all());

        $total = $coupons->count();

        $response = [
            'success' => 1,
            'code' => $total > 0 ? 200 : 204,
            'meta' => [
                'method' => 'GET',
                'endpoint' => '1/coupons',
                'limit' => $limit,
                'offset' => $offset,
                'total' => $total,
            ],
            'data' => CouponResource::collection($coupons),
            'errors' => [],
            'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
        ];

        return response()->json($response);
    }

    /**
     * Detail : Coupon
     * 
     * @param int $id
     * 
     * @return mixed
     */
    public function show(int $id)
    {
        try {
            $coupon = $this->couponService->getCouponById($id);

            $response = [
                'success' => 1,
                'code' => 200,
                'meta' => [
                    'method' => 'GET',
                    'endpoint' => '1/coupons/' . $id,
                ],
                'data' => new CouponResource($coupon),
                'errors' => [],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ];

            return response()->json($response);
        } catch (ModelNotFoundException $exception) {
            $response = [
                'success' => 0,
                'code' => 404,
                'meta' => [
                    'method' => 'GET',
                    'endpoint' => '1/coupons/' . $id,
                ],
                'data' => [],
                'errors' => [
                    'message' => 'The resource that matches the request ID does not found.',
                    'code' => 404002,
                ],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ];

            return response()->json($response, 404);
        }
    }

    /**
     * Store : Coupon
     * 
     * @param Request $request
     * 
     * @return mixed
     */
    public function store(Request $request)
    {
        $startTime = now();

        $validator = Validator::make($request->all(), Coupon::rules());

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'code' => 400,
                'meta' => [
                    'method' => 'POST',
                    'endpoint' => "1/coupons",
                ],
                'data' => [],
                'errors' => [
                    'message' => 'The request parameters are incorrect, please make sure to follow the documentation about request parameters of the resource.',
                    'code' => 400002,
                    'validation' => $validator->errors()->toArray(),
                ],
                'duration' => now()->diffInMilliseconds($startTime),
            ], 400);
        }

        DB::beginTransaction();
        try {
            $coupon = $this->couponService->storeCoupon($request->all());

            DB::commit();

            return $this->buildSuccessResponse($coupon, $startTime);
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->buildErrorResponse(['An unexpected error occurred.'], $startTime, 500);
        }
    }

    /**
     * Update a coupon.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $startTime = now();

        $duration = now()->diffInMilliseconds($startTime);

        $validator = Validator::make($request->all(), [
            'name' => 'max:128',
            'discount_type' => 'in:percentage,fix-amount',
            'amount' => 'integer',
            'code' => 'integer',
            'start_datetime' => 'date_format:Y-m-d H:i:s',
            'end_datetime' => 'date_format:Y-m-d H:i:s',
            'coupon_type' => 'in:public,private',
            'used_count' => 'integer',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'code' => 400,
                'meta' => [
                    'method' => 'PUT',
                    'endpoint' => "1/coupons",
                ],
                'data' => [],
                'errors' => [
                    'message' => 'The request parameters are incorrect, please make sure to follow the documentation about request parameters of the resource.',
                    'code' => 400002,
                    'validation' => $validator->errors()->toArray(),
                ],
                'duration' => $duration,
            ], 400);
        }

        $coupon = Coupon::find($id);

        if ($coupon) {
            $coupon->update($request->all());

            return response()->json([
                'success' => 1,
                'code' => 200,
                'meta' => [
                    'method' => 'PUT',
                    'endpoint' => "1/coupons/$id",
                ],
                'data' => [
                    'updated' => 1,
                ],
                'errors' => [],
                'duration' => $duration,
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'code' => 404,
                'meta' => [
                    'method' => 'PUT',
                    'endpoint' => "1/coupons/$id",
                ],
                'errors' => [
                    'message' => "The updating resource that corresponds to the ID wasn't found.",
                    "code" => 404003
                ],
                'duration' => $duration,
            ], 404);
        }
    }

    /**
     * Delete a coupon.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $coupon = $this->couponService->getCouponById($id);

        if ($coupon) {
            $coupon->delete();

            return response()->json([
                'success' => 1,
                'code' => 200,
                'meta' => [
                    'method' => 'DELETE',
                    'endpoint' => "1/coupons/$id",
                ],
                'data' => [
                    'deleted' => 1,
                ],
                'errors' => [],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ]);
        } else {
            return response()->json([
                'success' => 0,
                'code' => 404,
                'meta' => [
                    'method' => 'DELETE',
                    'endpoint' => "1/coupons/$id",
                ],
                'errors' => [
                    'message' => "The deleting resource that corresponds to the ID wasn't found.",
                ],
                'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
            ], 404);
        }
    }

    private function buildSuccessResponse($data, $startTime)
    {
        $duration = now()->diffInMilliseconds($startTime);

        return response()->json([
            'success' => 1,
            'code' => 201,
            'meta' => [
                'method' => 'POST',
                'endpoint' => '1/coupons',
            ],
            'data' => $data,
            'errors' => [],
            'duration' => $duration,
        ]);
    }

    private function buildErrorResponse($errors, $startTime, $statusCode = 400)
    {
        $formattedErrors = [];

        foreach ($errors->messages() as $attribute => $errorMessages) {
            foreach ($errorMessages as $errorMessage) {
                $formattedErrors[] = [
                    'attribute' => $attribute,
                    'errors' => [
                        [
                            'key' => 'required',
                            'message' => $errorMessage,
                        ],
                    ],
                ];
            }
        }

        $duration = now()->diffInMilliseconds($startTime);

        return response()->json([
            'success' => 0,
            'code' => $statusCode,
            'meta' => [
                'method' => 'POST',
                'endpoint' => '1/coupons',
            ],
            'data' => [],
            'errors' => [
                'message' => 'The request parameters are incorrect, please make sure to follow the documentation about request parameters of the resource.',
                'code' => 400002,
                'validation' => $formattedErrors,
            ],
            'duration' => $duration,
        ]);
    }
}
