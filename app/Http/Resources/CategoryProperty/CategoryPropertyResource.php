<?php

namespace App\Http\Resources\CategoryProperty;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class CategoryPropertyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'category_id' => $this->category_id,
            'property_key' => $this->property_key,
            'filter_type' => $this->filter_type ? $this->filter_type->toResource() : null,
            'unit' => $this->unit,
        ];
    }
}
