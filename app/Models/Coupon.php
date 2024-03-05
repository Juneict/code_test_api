<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Coupon extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'id',
        'name',
        'description',
        'discount_type',
        'amount',
        'image_url',
        'code',
        'start_datetime',
        'end_datetime',
        'coupon_type',
        'used_count',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    public static function rules()
    {
        return [
            'name' => 'required|string|max:128',
            'discount_type' => 'required|string|in:percentage,fixed',
            'amount' => 'required|integer',
            'code' => 'required|integer',
            'start_datetime' => 'required|date_format:Y-m-d H:i:s',
            'end_datetime' => 'required|date_format:Y-m-d H:i:s',
            'coupon_type' => 'required|string|in:public,private',
            'used_count' => 'nullable|integer',
        ];
    }

    public function shops()
    {
        return $this->belongsToMany(Shop::class, 'coupon_shops')
            ->withTimestamps();
    }

}
