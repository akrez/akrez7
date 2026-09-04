<?php

namespace App\Services;

use App\Data\Category\StoreCategoryData;
use App\Data\Category\UpdateCategoryData;
use App\Http\Resources\Category\CategoryCollection;
use App\Http\Resources\Category\CategoryResource;
use App\Models\Category;
use App\Support\ApiResponse;
use App\Support\WebResponse;

class CategoryService extends Service
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
            'category' => (new CategoryResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'categories' => (new CategoryCollection($models))->toArr(),
        ]);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return Category::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestCategories(int $blogId)
    {
        $categories = $this->getLatestBlogQuery($blogId)->get();

        return WebResponse::new()->data([
            'categories' => (new CategoryCollection($categories))->toArr(),
        ]);
    }

    public function storeCategory(StoreCategoryData $storeCategoryData)
    {
        $webResponse = WebResponse::new()->input($storeCategoryData);

        $validation = $storeCategoryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $category = Category::create([
            'name' => $storeCategoryData->name,
            'blog_id' => $storeCategoryData->blog_id,
        ]);
        if (! $category) {
            return $webResponse->status(500);
        }

        return $webResponse->status(201)->data($category)->message(__(':name is created successfully', [
            'name' => __('Category'),
        ]));
    }

    public function getCategory(int $blogId, int $id)
    {
        $category = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $category) {
            return WebResponse::new()->status(404);
        }

        return WebResponse::new()->data([
            'category' => (new CategoryResource($category))->toArr(),
        ]);
    }

    public function updateCategory(UpdateCategoryData $updateCategoryData)
    {
        $webResponse = WebResponse::new()->input($updateCategoryData);

        $validation = $updateCategoryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $category = $this->getLatestBlogQuery($updateCategoryData->blog_id)->where('id', $updateCategoryData->id)->first();
        if (! $category) {
            return $webResponse->status(404);
        }

        $category->update([
            'name' => $updateCategoryData->name,
        ]);
        if (! $category->save()) {
            return $webResponse->status(500);
        }

        return $webResponse
            ->status(201)
            ->data(['category' => (new CategoryResource($category))->toArr()])
            ->message(__(':name is updated successfully', [
                'name' => $category->name,
            ]));
    }
}
