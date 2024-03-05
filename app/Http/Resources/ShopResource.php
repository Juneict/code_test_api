<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ShopResource extends JsonResource
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
            'query' => $this->query,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'zoom' => $this->zoom,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
