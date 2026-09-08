<?php

namespace App\Services;

use App\Data\CategoryProperty\StoreCategoryPropertyData;
use App\Data\CategoryProperty\UpdateCategoryPropertyData;
use App\Http\Resources\CategoryProperty\CategoryPropertyCollection;
use App\Http\Resources\CategoryProperty\CategoryPropertyResource;
use App\Models\CategoryProperty;
use App\Support\ApiResponse;
use App\Support\WebResponse;

class CategoryPropertyService extends Service
{
    public static function new()
    {
        return app(self::class);
    }

    public function getApiResource(int $blogId, int $id): ApiResponse
    {
        $model = $this->getLatestApiQuery($blogId)
            ->where('id', $id)
            ->first();

        return ApiResponse::new(200)->data([
            'category_property' => (new CategoryPropertyResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'category_properties' => (new CategoryPropertyCollection($models))->toArr(),
        ]);
    }

    public function getApiCollectionByCategoryIds(int $blogId, array $categoryIds): ApiResponse
    {
        $query = $this->getLatestApiQuery($blogId)
            ->where(function ($query) use ($categoryIds) {
                $query->whereNull('category_id')
                    ->orWhereIn('category_id', array_values($categoryIds));
            });

        return ApiResponse::new(200)->data([
            'category_properties' => (new CategoryPropertyCollection($query->get()))->toArr(),
        ]);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return CategoryProperty::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestCategoryProperties(int $blogId)
    {
        $categoryProperties = $this->getLatestBlogQuery($blogId)->get();

        return WebResponse::new()->data([
            'category_properties' => (new CategoryPropertyCollection($categoryProperties))->toArr(),
        ]);
    }

    public function storeCategoryProperty(StoreCategoryPropertyData $storeCategoryPropertyData)
    {
        $webResponse = WebResponse::new()->input($storeCategoryPropertyData);

        $validation = $storeCategoryPropertyData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $categoryProperty = CategoryProperty::create([
            'blog_id' => $storeCategoryPropertyData->blog_id,
            'category_id' => $storeCategoryPropertyData->category_id,
            'property_key' => $storeCategoryPropertyData->property_key,
            'filter_type' => $storeCategoryPropertyData->filter_type,
            'unit' => $storeCategoryPropertyData->unit,
        ]);
        if (! $categoryProperty) {
            return $webResponse->status(500);
        }

        return $webResponse->status(201)->data($categoryProperty)->message(__(':name is created successfully', [
            'name' => __('Property'),
        ]));
    }

    public function getCategoryProperty(int $blogId, int $id)
    {
        $categoryProperty = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $categoryProperty) {
            return WebResponse::new()->status(404);
        }

        return WebResponse::new()->data([
            'category_property' => (new CategoryPropertyResource($categoryProperty))->toArr(),
        ]);
    }

    public function updateCategoryProperty(UpdateCategoryPropertyData $updateCategoryPropertyData)
    {
        $webResponse = WebResponse::new()->input($updateCategoryPropertyData);

        $validation = $updateCategoryPropertyData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $categoryProperty = $this->getLatestBlogQuery($updateCategoryPropertyData->blog_id)->where('id', $updateCategoryPropertyData->id)->first();
        if (! $categoryProperty) {
            return $webResponse->status(404);
        }

        $categoryProperty->update([
            'category_id' => $updateCategoryPropertyData->category_id,
            'property_key' => $updateCategoryPropertyData->property_key,
            'filter_type' => $updateCategoryPropertyData->filter_type,
            'unit' => $updateCategoryPropertyData->unit,
        ]);
        if (! $categoryProperty->save()) {
            return $webResponse->status(500);
        }

        return $webResponse
            ->status(201)
            ->data(['category_property' => (new CategoryPropertyResource($categoryProperty))->toArr()])
            ->message(__(':name is updated successfully', [
                'name' => $categoryProperty->property_key,
            ]));
    }
}
