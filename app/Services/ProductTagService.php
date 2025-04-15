<?php

namespace App\Services;

use App\Data\ProductTag\StoreProductTagData;
use App\Http\Resources\ProductTag\ProductTagCollection;
use App\Http\Resources\ProductTag\ProductTagResource;
use App\Models\ProductTag;
use App\Support\ApiResponse;
use App\Support\WebResponse;

class ProductTagService extends Service
{
    const TAG_NAME_MAX_LENGTH = 32;

    const NAME_SEPARATORS = [
        PHP_EOL => 'Enter',
        ':' => ':',
        ',' => ',',
        '،' => '،',
        "\t" => 'Tab',
    ];

    const NAME_GLUE = PHP_EOL;

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
            'product_tag' => (new ProductTagResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'product_tags' => (new ProductTagCollection($models))->toArr(),
        ]);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return ProductTag::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function exportToText(int $blogId, int $productId)
    {
        return $this->getProductTagQuery($blogId, $productId)
            ->get()
            ->pluck('tag_name')
            ->implode(ProductTagService::NAME_GLUE);
    }

    public function storeProductTag(StoreProductTagData $storeProductTagData)
    {
        $webResponse = WebResponse::new()->input($storeProductTagData);

        $validation = $storeProductTagData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $this->getProductTagQuery(
            $storeProductTagData->blog_id,
            $storeProductTagData->product_id
        )->delete();

        $createdTagNames = [];
        foreach ($storeProductTagData->safe_tag_names as $tagName) {
            $createdTagNames[] = ProductTag::create([
                'blog_id' => $storeProductTagData->blog_id,
                'product_id' => $storeProductTagData->product_id,
                'tag_name' => $tagName,
            ]);
        }

        if (count($createdTagNames) == 0) {
            return $webResponse->status(200)->message(__('All :names removed', [
                'names' => __('Tags'),
            ]));
        }

        return $webResponse->status(201)->message(__(':count :names are created successfully', [
            'count' => count($createdTagNames),
            'names' => __('Tag'),
        ]));
    }

    protected function getProductTagQuery(int $blogId, int $productId)
    {
        return $this->getLatestBlogQuery($blogId)
            ->where('product_id', $productId);
    }
}
