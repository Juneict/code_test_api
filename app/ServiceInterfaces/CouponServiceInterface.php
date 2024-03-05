<?php

namespace App\ServiceInterfaces;

interface CouponServiceInterface 
{
    /**
     * Service: Get all Coupons.
     * 
     * @param array $columns
     * 
     * @return Colection
     */
    public function getAllCoupons(array $params);

    /**
     * Service: Get Coupon By Id.
     * 
     * @param int $id
     * 
     * @throws ModelNotFoundException
     * 
     * @return Coupon
     */
    public function getCouponById(int $id);

    /**
     * Service: Store Coupon. 
     * 
     * @param array $columns
     * 
     * @return Coupon
     */
    public function storeCoupon(array $params);
}