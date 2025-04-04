<?php

namespace App\Services;

use App\Models\ProductTag;
use App\Support\ApiResponse;
use App\Support\WebResponse;
use App\Data\ProductTag\StoreProductTagData;
use App\Http\Resources\ProductTag\ProductTagCollection;

class ProductTagService
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

    public function getApiCollection(int $blogId)
    {
        $productTags = $this->getProductTagQuery($blogId)
            ->defaultOrder()
            ->get();

        return ApiResponse::new(200)->data([
            'productTags' => (new ProductTagCollection($productTags))->toArray(request()),
        ]);
    }

    protected function getProductTagQuery($blogId)
    {
        return ProductTag::query()->where('blog_id', $blogId);
    }

    public function exportToText(int $blogId, int $productId)
    {
        return $this->getProductTagsQuery($blogId, $productId)
            ->defaultOrder()
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

        $this->getProductTagsQuery(
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

    protected function getProductTagsQuery(int $blogId, int $productId)
    {
        return ProductTag::query()
            ->where('blog_id', $blogId)
            ->where('product_id', $productId);
    }
}
