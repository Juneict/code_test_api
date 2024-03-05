<?php

namespace App\ServiceInterfaces;

interface ShopServiceInterface 
{
    /**
     * Service: Get all Shops.
     * 
     * @param array $columns
     * 
     * @return Colection
     */
    public function getAllShops(array $params);

    /**
     * Service: Get Shop By Id.
     * 
     * @param int $id
     * 
     * @throws ModelNotFoundException
     * 
     * @return Shop
     */
    public function getShopById(int $id);

    /**
     * Service: Store Shop. 
     * 
     * @param array $columns
     * 
     * @return Coupon
     */
    public function storeShop(array $params);
}