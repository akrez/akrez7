<?php

namespace App\Http\Resources\Gallery;

use App\Http\Resources\JsonResource;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class GalleryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $galleryService = GalleryService::new();

        return [
            'id' => $this->id,
            'name' => $this->name,
            'gallery_category' => $this->gallery_category ? $this->gallery_category->toResource() : null,
            'gallery_id' => $this->gallery_id,
            'selected_at' => $this->formatCarbonDateTime($this->selected_at),
            'created_at' => $this->formatCarbonDateTime($this->created_at),
            'gallery_order' => $this->gallery_order,
            'base_url' => $galleryService->getBaseUrl($this->resource->gallery_category->value),
            'url' => $galleryService->getUrlByModel($this->resource),
            'contain_url' => $galleryService->getUrlByModel($this->resource, '__contain'),
        ];
    }
}
