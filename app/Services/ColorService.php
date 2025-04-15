<?php

namespace App\Services;

use App\Data\Color\StoreColorData;
use App\Data\Color\UpdateColorData;
use App\Http\Resources\Color\ColorCollection;
use App\Http\Resources\Color\ColorResource;
use App\Models\Color;
use App\Support\ApiResponse;
use App\Support\WebResponse;

class ColorService extends Service
{
    public static function new()
    {
        return app(self::class);
    }

    public function getApiResource(int $blogId, int $id): ApiResponse
    {
        $model = $this->getLatestApiQuery($blogId)
            ->first();

        return ApiResponse::new(200)->data([
            'color' => (new ColorResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'colors' => (new ColorCollection($models))->toArr(),
        ]);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return Color::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestColors(int $blogId)
    {
        $colors = $this->getLatestBlogQuery($blogId)->get();

        return WebResponse::new()->data([
            'colors' => (new ColorCollection($colors))->toArr(),
        ]);
    }

    public function storeColor(StoreColorData $storeColorData)
    {
        $webResponse = WebResponse::new()->input($storeColorData);

        $validation = $storeColorData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $color = Color::create([
            'code' => $storeColorData->code,
            'name' => $storeColorData->name,
            'blog_id' => $storeColorData->blog_id,
        ]);
        if (! $color) {
            return $webResponse->status(500);
        }

        return $webResponse->status(201)->data($color)->message(__(':name is created successfully', [
            'name' => __('Color'),
        ]));
    }

    public function getColor(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $color = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $color) {
            return $webResponse->status(404);
        }

        return WebResponse::new()->data([
            'color' => (new ColorResource($color))->toArr(),
        ]);
    }

    public function updateColor(UpdateColorData $updateColorData)
    {
        $webResponse = WebResponse::new()->input($updateColorData);

        $validation = $updateColorData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $color = $this->getLatestBlogQuery($updateColorData->blog_id)->where('id', $updateColorData->id)->first();
        if (! $color) {
            return $webResponse->status(404);
        }

        $color->update([
            'name' => $updateColorData->name,
            'code' => $updateColorData->code,
        ]);
        if (! $color->save()) {
            return $webResponse->status(500);
        }

        return $webResponse
            ->status(201)
            ->data(['color' => (new ColorResource($color))->toArr()])
            ->message(__(':name is updated successfully', [
                'name' => $color->name,
            ]));
    }
}
