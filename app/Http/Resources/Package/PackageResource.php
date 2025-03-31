<?php

namespace App\Http\Resources\Package;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class PackageResource extends JsonResource
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
            'package_status' => $this->package_status ? $this->package_status->toResource() : null,
            'price' => $this->price,
            'color_id' => $this->color_id,
            'guaranty' => $this->guaranty,
            'description' => $this->description,
        ];
    }
}
