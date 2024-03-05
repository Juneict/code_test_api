<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class CouponShop extends Pivot
{
    use HasFactory;

    protected $fillable = [
        'id',
        'coupon_id',
        'shop_id',
        'created_at',
        'updated_at'
    ];

    public static function rules()
    {
        return [
            'shop_id' => 'required|integer',
        ];
    }
}
