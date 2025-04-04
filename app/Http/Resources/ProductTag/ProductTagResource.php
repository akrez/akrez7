<?php

namespace App\Http\Resources\ProductTag;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class ProductTagResource extends JsonResource
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
            'tag_name' => $this->resource->tag_name,
        ];
    }
}
