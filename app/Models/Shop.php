<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Shop extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'query',
        'latitude',
        'longitude',
        'zoom',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public static function rules()
    {
        return [
            'name' => 'required|string',
            'query' => 'string',
            'latitude' => 'numeric',
            'longitude' => 'numeric',
            'zoom' => 'string',
        ];
    }

    public function coupons()
    {
        return $this->belongsToMany(Coupon::class, 'coupon_shops')
            ->withTimestamps();
    }
}
