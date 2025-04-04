<?php

namespace App\Http\Resources\ProductProperty;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class ProductPropertyResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'product_id' => $this->resource->product_id,
            'property_key' => $this->resource->property_key,
            'property_value' => $this->resource->property_value,
        ];
    }
}
