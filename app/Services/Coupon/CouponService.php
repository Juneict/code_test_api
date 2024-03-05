<?php

namespace App\Services\Coupon;

use App\Models\Coupon;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use App\ServiceInterfaces\CouponServiceInterface;

class CouponService implements CouponServiceInterface
{
    /**
     * Service: Get All Coupons.
     * 
     * @param array $columns
     * 
     * @return Colection
     */
    public function getAllCoupons(array $params)
    {
        $query = Coupon::query();

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        $coupons = $query->offset(config('coupon.offset'))
            ->limit(config('coupon.limit'))
            ->paginate(config('coupon.limit'));

        return $coupons;
    }

    /**
     * Service: Get Coupon By Id.
     * 
     * @param int $id
     * 
     * @throws ModelNotFoundException
     * 
     * @return Coupon
     */
    public function getCouponById(int $id): Coupon
    {
        return Coupon::findOrFail($id);
    }

    /**
     * Service: Store Coupon. 
     * 
     * @param array $columns
     * 
     * @return Coupon
     */
    public function storeCoupon(array $params): Coupon
    {
        return DB::transaction(function () use ($params) {
            $coupon = Coupon::create($params);

            return $coupon;
        });
    }

    /**
     * Service: Update Coupon.
     * 
     * @param int $params
     * @param array $params
     * 
     * @throws ModelNotFoundException
     * 
     * @return bool
     */
    public function updateCoupon(int $id, array $params): bool
    {
        return DB::transaction(function () use ($id, $params) {
            $coupon = Coupon::findOrFail($id);

            return $coupon->update($params);
        });
    }
}
