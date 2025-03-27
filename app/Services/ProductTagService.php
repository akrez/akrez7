<?php

namespace App\Services;

use App\Data\ProductTag\StoreProductTagData;
use App\Models\ProductTag;
use App\Support\ResponseBuilder;

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

    public function exportToTextArea(int $blogId, int $product_id)
    {
        return implode(ProductTagService::NAME_GLUE, $this->exportToArray($blogId, $product_id));
    }

    public function storeProductTag(StoreProductTagData $storeProductTagData)
    {
        $responseBuilder = ResponseBuilder::new()->input($storeProductTagData);

        $validation = $storeProductTagData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $this->getLatestProductTagsQuery(
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
            return $responseBuilder->status(200)->message(__('All :names removed', [
                'names' => __('Tags'),
            ]));
        }

        return $responseBuilder->status(201)->message(__(':count :names are created successfully', [
            'count' => count($createdTagNames),
            'names' => __('Tag'),
        ]));
    }

    protected function exportToArray(int $blogId, int $productId)
    {
        return $this->getLatestProductTagsQuery($blogId, $productId)
            ->get()
            ->pluck('tag_name')
            ->toArray();
    }

    protected function getLatestProductTagsQuery(int $blogId, int $productId)
    {
        return ProductTag::query()
            ->where('blog_id', $blogId)
            ->where('product_id', $productId);
    }
}
