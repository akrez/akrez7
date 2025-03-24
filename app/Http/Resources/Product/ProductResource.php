<?php

namespace App\Http\Resources\Product;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class ProductResource extends JsonResource
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
            'name' => $this->name,
            'code' => $this->code,
            'product_order' => $this->product_order,
            'product_status' => $this->product_status ? $this->product_status->toResource() : null,
            'created_at' => $this->formatCarbonDateTime($this->created_at),
            'updated_at' => $this->formatCarbonDateTime($this->updated_at),
        ];
    }
}
