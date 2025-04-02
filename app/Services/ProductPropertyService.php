<?php

namespace App\Services;

use App\Data\ProductProperty\StoreProductPropertyData;
use App\Models\ProductProperty;
use App\Support\WebResponse;
use Illuminate\Support\Collection;

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
        $responseBuilder = WebResponse::new()->input($storeProductPropertyData);

        $validation = $storeProductPropertyData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
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
            return $responseBuilder->status(200)->message(__('All :names removed', [
                'names' => __('Properties'),
            ]));
        }

        return $responseBuilder->status(201)->message(__(':count :names are created successfully', [
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
