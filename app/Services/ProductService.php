<?php

namespace App\Services;

use App\Data\Product\StoreProductData;
use App\Data\Product\UpdateProductData;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Support\ResponseBuilder;

class ProductService
{
    public static function new()
    {
        return app(self::class);
    }

    protected function getQuery($blogId)
    {
        return Product::query()->where('blog_id', $blogId);
    }

    public function getLatestGalleries(int $blogId)
    {
        $products = $this->getQuery($blogId)->get();

        return ResponseBuilder::new()->data([
            'products' => (new ProductCollection($products))->toArray(request()),
        ]);
    }

    public function storeProduct(StoreProductData $storeProductData)
    {
        $responseBuilder = ResponseBuilder::new()->input($storeProductData);

        $validation = $storeProductData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $product = Product::create([
            'code' => $storeProductData->code,
            'name' => $storeProductData->name,
            'product_status' => $storeProductData->product_status,
            'product_order' => $storeProductData->product_order,
            'blog_id' => $storeProductData->blog_id,
        ]);
        if (! $product) {
            return $responseBuilder->status(500);
        }

        return $responseBuilder->status(201)->data($product)->message(__(':name is created successfully', [
            'name' => __('Product'),
        ]));
    }

    public function getProduct(int $blogId, int $id)
    {
        $responseBuilder = ResponseBuilder::new();

        $product = $this->getQuery($blogId)->where('id', $id)->first();
        if (! $product) {
            return $responseBuilder->status(404);
        }

        return ResponseBuilder::new()->data([
            'product' => (new ProductResource($product))->toArr(request()),
        ]);
    }

    public function updateProduct(UpdateProductData $updateProductData)
    {
        $responseBuilder = ResponseBuilder::new()->input($updateProductData);

        $validation = $updateProductData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $product = $this->getQuery($updateProductData->blog_id)->where('id', $updateProductData->id)->first();
        if (! $product) {
            return $responseBuilder->status(404);
        }

        $product->update([
            'code' => $updateProductData->code,
            'name' => $updateProductData->name,
            'product_status' => $updateProductData->product_status,
            'product_order' => $updateProductData->product_order,
            'blog_id' => $updateProductData->blog_id,
        ]);
        if (! $product->save()) {
            return $responseBuilder->status(500);
        }

        return $responseBuilder
            ->status(201)
            ->data(['product' => (new ProductResource($product))->toArr(request())])
            ->message(__(':name is updated successfully', [
                'name' => $product->name,
            ]));
    }
}
