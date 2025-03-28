<?php

namespace App\Http\Controllers;

use App\Data\ProductProperty\StoreProductPropertyData;
use App\Services\ProductPropertyService;
use App\Services\ProductService;
use Illuminate\Http\Request;

class ProductPropertyController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ProductPropertyService $productPropertieservice
    ) {}

    public function create(int $product_id)
    {
        $response = $this->productService->getProduct($this->blogId(), $product_id)->abortUnSuccessful();

        $product = $response->getData('product');

        return view('product_property.create', [
            'product' => $product,
            'productPropertiesText' => $this->productPropertieservice->exportToText($this->blogId(), $product['id']),
        ]);
    }

    public function store(Request $request, int $product_id)
    {
        $response = $this->productService->getProduct($this->blogId(), $product_id)->abortUnSuccessful();

        $product = $response->getData('product');

        return $this->productPropertieservice->storeProductProperty(new StoreProductPropertyData(
            $this->blogId(),
            $product['id'],
            $request->keys_values
        ));
    }
}
