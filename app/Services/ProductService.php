<?php

namespace App\Services;

use App\Data\Product\StoreProductData;
use App\Data\Product\UpdateProductData;
use App\Enums\ProductStatusEnum;
use App\Http\Resources\Product\ProductCollection;
use App\Http\Resources\Product\ProductResource;
use App\Models\Product;
use App\Support\ApiResponse;
use App\Support\WebResponse;

class ProductService extends Service
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
            'product' => (new ProductResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'products' => (new ProductCollection($models))->toArr(),
        ]);
    }

    protected function getLatestApiQuery($blogId)
    {
        return $this->getLatestBaseQuery($blogId)
            ->where('product_status', ProductStatusEnum::ACTIVE->value);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return Product::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestProducts(int $blogId)
    {
        $products = $this->getLatestBlogQuery($blogId)->get();

        return WebResponse::new()->data([
            'products' => (new ProductCollection($products))->toArr(),
        ]);
    }

    public function storeProduct(StoreProductData $storeProductData)
    {
        $webResponse = WebResponse::new()->input($storeProductData);

        $validation = $storeProductData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $product = Product::create([
            'code' => $storeProductData->code,
            'name' => $storeProductData->name,
            'product_status' => $storeProductData->product_status,
            'product_order' => $storeProductData->product_order,
            'blog_id' => $storeProductData->blog_id,
        ]);
        if (! $product) {
            return $webResponse->status(500);
        }

        return $webResponse->status(201)->data($product)->message(__(':name is created successfully', [
            'name' => __('Product'),
        ]));
    }

    public function getProduct(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $product = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $product) {
            return $webResponse->status(404);
        }

        return WebResponse::new()->data([
            'product' => (new ProductResource($product))->toArr(),
        ]);
    }

    public function updateProduct(UpdateProductData $updateProductData)
    {
        $webResponse = WebResponse::new()->input($updateProductData);

        $validation = $updateProductData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $product = $this->getLatestBlogQuery($updateProductData->blog_id)->where('id', $updateProductData->id)->first();
        if (! $product) {
            return $webResponse->status(404);
        }

        $product->update([
            'code' => $updateProductData->code,
            'name' => $updateProductData->name,
            'product_status' => $updateProductData->product_status,
            'product_order' => $updateProductData->product_order,
            'blog_id' => $updateProductData->blog_id,
        ]);
        if (! $product->save()) {
            return $webResponse->status(500);
        }

        return $webResponse
            ->status(201)
            ->data(['product' => (new ProductResource($product))->toArr()])
            ->message(__(':name is updated successfully', [
                'name' => $product->name,
            ]));
    }
}
