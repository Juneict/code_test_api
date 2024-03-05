<?php

namespace App\Services\Shop;

use App\Models\Shop;
use Illuminate\Support\Facades\DB;
use App\ServiceInterfaces\ShopServiceInterface;


class ShopService implements ShopServiceInterface
{
    /**
     * Service: Get All Shops.
     * 
     * @param array $columns
     * 
     * @return Colection
     */
    public function getAllShops(array $params)
    {
        $query = Shop::query();

        if (isset($params['name'])) {
            $query->where('name', 'like', '%' . $params['name'] . '%');
        }

        $shops = $query->offset(config('shop.offset'))
            ->limit(config('shop.limit'))
            ->paginate(config('shop.limit'));

        return $shops;
    }

    /**
     * Service: Get Shop By Id.
     * 
     * @param int $id
     * 
     * @throws ModelNotFoundException
     * 
     * @return Shop
     */
    public function getShopById(int $id): Shop
    {
        return Shop::findOrFail($id);
    }

    /**
     * Service: Store Shop. 
     * 
     * @param array $columns
     * 
     * @return Shop
     */
    public function storeShop(array $params): Shop
    {
        return DB::transaction(function () use ($params) {
            $shop = Shop::create($params);

            return $shop;
        });
    }

    /**
     * Service: Update Shop.
     * 
     * @param int $params
     * @param array $params
     * 
     * @throws ModelNotFoundException
     * 
     * @return bool
     */
    public function updateShop(int $id, array $params): bool
    {
        return DB::transaction(function () use ($id, $params) {
            $shop = Shop::findOrFail($id);

            return $shop->update($params);
        });
    }
}
