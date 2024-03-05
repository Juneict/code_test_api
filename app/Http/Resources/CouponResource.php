<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CouponResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'admin_id' => $this->admin_id,
            'name' => $this->name,
            'description' => $this->description,
            'discount_type' => $this->discount_type,
            'amount' => $this->amount,
            'image_url' => $this->image_url,
            'code' => $this->code,
            'start_datetime' => $this->start_datetime,
            'end_datetime' => $this->end_datetime,
            'coupon_type' => $this->coupon_type,
            'used_count' => $this->used_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
