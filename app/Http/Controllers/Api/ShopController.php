<?php

namespace App\Http\Controllers\Api;

use App\Models\Shop;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\ShopResource;
use Illuminate\Support\Facades\Validator;
use App\ServiceInterfaces\ShopServiceInterface;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class ShopController extends Controller
{
    /** @var $shopService */
    private $shopService;

    public function __construct(
        ShopServiceInterface $shopService
    ) {
        $this->shopService = $shopService;
    }

    /**
     * List : Shops
     * 
     * @param Request $request
     * 
     * @return mixed
     */
    public function index(Request $request)
    {
        $limit = $request->input('limit', config('shop.limit'));
        $offset = $request->input('offset', config('shop.offset'));

        $shop = $this->shopService->getAllShops($request->all());
        
        $total = $shop->count();

        $response = [
            'success' => 1,
            'code' => $total > 0 ? 200 : 204,
            'meta' => [
                'method' => 'GET',
                'endpoint' => '1/shops',
                'limit' => $limit,
                'offset' => $offset,
                'total' => $total,
            ],
            'data' => ShopResource::collection($shop),
            'errors' => [],
            'duration' => microtime(true) - $_SERVER["REQUEST_TIME_FLOAT"],
        ];

        return response()->json($response);
    }

    /**
     * Detail : Shop
     * 
     * @param int $id
     * 
     * @return mixed
     */
    public function show(int $id)
    {
        try {
            $shop = $this->shopService->getShopById($id);

            $response = [
                'success' => 1,
                'code' => 200,
                'meta' => [
                    'method' => 'GET',
                    'endpoint' => '1/shops/' . $id,
                ],
                'data' => new ShopResource($shop),
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
                    'endpoint' => '1/shops/' . $id,
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
     * Store : Shop
     * 
     * @param Request $request
     * 
     * @return mixed
     */
    public function store(Request $request)
    {
        $startTime = now();

        $validator = Validator::make($request->all(), Shop::rules());

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'code' => 400,
                'meta' => [
                    'method' => 'POST',
                    'endpoint' => "1/shops",
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
            $shop = $this->shopService->storeShop($request->all());

            DB::commit();

            return $this->buildSuccessResponse($shop, $startTime);
        } catch (\Exception $exception) {
            DB::rollBack();

            return $this->buildErrorResponse(['An unexpected error occurred.'], $startTime, 500);
        }
    }

     /**
     * Update a shop.
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
            'name' => 'string',
            'query' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'zoom' => 'string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => 0,
                'code' => 400,
                'meta' => [
                    'method' => 'PUT',
                    'endpoint' => "1/shops",
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

        $shop = Shop::find($id);

        if ($shop) {
            $shop->update($request->all());

            return response()->json([
                'success' => 1,
                'code' => 200,
                'meta' => [
                    'method' => 'PUT',
                    'endpoint' => "1/shops/$id",
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
                    'endpoint' => "1/shops/$id",
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
     * Delete a shop.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id)
    {
        $shop = Shop::find($id);

        if ($shop) {
            $shop->delete();

            return response()->json([
                'success' => 1,
                'code' => 200,
                'meta' => [
                    'method' => 'DELETE',
                    'endpoint' => "1/shops/$id",
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
                    'endpoint' => "1/shops/$id",
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
                'endpoint' => '1/shops',
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
                'endpoint' => '1/shops',
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
