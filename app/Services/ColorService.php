<?php

namespace App\Services;

use App\Data\Color\StoreColorData;
use App\Data\Color\UpdateColorData;
use App\Http\Resources\Color\ColorCollection;
use App\Http\Resources\Color\ColorResource;
use App\Models\Color;
use App\Support\ResponseBuilder;

class ColorService
{
    public static function new()
    {
        return app(self::class);
    }

    protected function getQuery($blogId)
    {
        return Color::query()->where('blog_id', $blogId);
    }

    public function getLatestColors(int $blogId)
    {
        $colors = $this->getQuery($blogId)->get();

        return ResponseBuilder::new()->data([
            'colors' => (new ColorCollection($colors))->toArray(request()),
        ]);
    }

    public function storeColor(StoreColorData $storeColorData)
    {
        $responseBuilder = ResponseBuilder::new()->input($storeColorData);

        $validation = $storeColorData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $color = Color::create([
            'code' => $storeColorData->code,
            'name' => $storeColorData->name,
            'blog_id' => $storeColorData->blog_id,
        ]);
        if (! $color) {
            return $responseBuilder->status(500);
        }

        return $responseBuilder->status(201)->data($color)->message(__(':name is created successfully', [
            'name' => __('Color'),
        ]));
    }

    public function getColor(int $blogId, int $id)
    {
        $responseBuilder = ResponseBuilder::new();

        $color = $this->getQuery($blogId)->where('id', $id)->first();
        if (! $color) {
            return $responseBuilder->status(404);
        }

        return ResponseBuilder::new()->data([
            'color' => (new ColorResource($color))->toArr(request()),
        ]);
    }

    public function updateColor(UpdateColorData $updateColorData)
    {
        $responseBuilder = ResponseBuilder::new()->input($updateColorData);

        $validation = $updateColorData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $color = $this->getQuery($updateColorData->blog_id)->where('id', $updateColorData->id)->first();
        if (! $color) {
            return $responseBuilder->status(404);
        }

        $color->update([
            'name' => $updateColorData->name,
            'code' => $updateColorData->code,
        ]);
        if (! $color->save()) {
            return $responseBuilder->status(500);
        }

        return $responseBuilder
            ->status(201)
            ->data(['color' => (new ColorResource($color))->toArr(request())])
            ->message(__(':name is updated successfully', [
                'name' => $color->name,
            ]));
    }
}
