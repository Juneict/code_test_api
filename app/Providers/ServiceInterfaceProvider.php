<?php

namespace App\Providers;

use App\ServiceInterfaces\CouponServiceInterface;
use App\ServiceInterfaces\ShopServiceInterface;
use App\Services\Coupon\CouponService;
use App\Services\Shop\ShopService;
use Illuminate\Support\ServiceProvider;

class ServiceInterfaceProvider extends ServiceProvider
{
     /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            CouponServiceInterface::class,
            CouponService::class,
        );

        $this->app->bind(
            ShopServiceInterface::class,
            ShopService::class,
        );
    }
}