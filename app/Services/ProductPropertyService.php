<?php

namespace App\Services;

use App\Support\ApiResponse;
use App\Support\WebResponse;
use App\Models\ProductProperty;
use Illuminate\Support\Collection;
use App\Data\ProductProperty\StoreProductPropertyData;
use App\Http\Resources\ProductProperty\ProductPropertyCollection;

class ProductPropertyService
{
    const PROPERTY_MAX_LENGTH = 32;

    const KEY_VALUES_SEPARATORS = [
        ':' => ':',
        ',' => ',',
        '،' => '،',
        "\t" => 'Tab',
    ];

    const KEY_VALUES_GLUE = ':';

    const VALUES_GLUE = ',';

    const LINES_SEPARATORS = [
        PHP_EOL => 'Enter',
    ];

    const LINES_GLUE = PHP_EOL;

    public static function new()
    {
        return app(self::class);
    }

    public function getApiCollection(int $blogId)
    {
        $productProperties = $this->getProductPropertyQuery($blogId)
            ->defaultOrder()
            ->get();

        return ApiResponse::new(200)->data([
            'productProperties' => (new ProductPropertyCollection($productProperties))->toArray(request()),
        ]);
    }

    protected function getProductPropertyQuery($blogId)
    {
        return ProductProperty::query()->where('blog_id', $blogId);
    }

    public function exportToText(int $blogId, int $productId)
    {
        return $this->getProductPropertiesQuery($blogId, $productId)
            ->defaultOrder()
            ->get()
            ->groupBy('property_key')
            ->map(function (Collection $groupedValues, $key) {
                return $key.ProductPropertyService::KEY_VALUES_GLUE.' '.$groupedValues
                    ->pluck('property_value')
                    ->implode(ProductPropertyService::VALUES_GLUE.' ');
            })
            ->implode(ProductPropertyService::LINES_GLUE);
    }

    public function storeProductProperty(StoreProductPropertyData $storeProductPropertyData)
    {
        $webResponse = WebResponse::new()->input($storeProductPropertyData);

        $validation = $storeProductPropertyData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $this->getProductPropertiesQuery(
            $storeProductPropertyData->blog_id,
            $storeProductPropertyData->product_id
        )->delete();

        $createdKeyValues = [];
        foreach ($storeProductPropertyData->safe_keys_values as $keyValues) {
            foreach ($keyValues['property_values'] as $value) {
                $createdKeyValues[] = ProductProperty::create([
                    'blog_id' => $storeProductPropertyData->blog_id,
                    'product_id' => $storeProductPropertyData->product_id,
                    'property_key' => $keyValues['property_key'],
                    'property_value' => $value,
                ]);
            }
        }

        if (count($createdKeyValues) == 0) {
            return $webResponse->status(200)->message(__('All :names removed', [
                'names' => __('Properties'),
            ]));
        }

        return $webResponse->status(201)->message(__(':count :names are created successfully', [
            'count' => count($createdKeyValues),
            'names' => __('Property'),
        ]));
    }

    protected function getProductPropertiesQuery(int $blogId, int $productId)
    {
        return ProductProperty::query()
            ->where('blog_id', $blogId)
            ->where('product_id', $productId);
    }
}
