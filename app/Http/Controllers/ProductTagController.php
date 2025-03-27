<?php

namespace App\Http\Controllers;

use App\Data\ProductTag\StoreProductTagData;
use App\Services\ProductService;
use App\Services\ProductTagService;
use Illuminate\Http\Request;

class ProductTagController extends Controller
{
    public function __construct(
        protected ProductService $productService,
        protected ProductTagService $productTagService
    ) {}

    public function create(int $product_id)
    {
        $response = $this->productService->getProduct($this->blogId(), $product_id)->abortUnSuccessful();

        $product = $response->getData('product');

        return view('product_tag.create', [
            'product' => $product,
            'productTagsText' => $this->productTagService->exportToTextArea($this->blogId(), $product['id']),
        ]);
    }

    public function store(Request $request, int $product_id)
    {
        $response = $this->productService->getProduct($this->blogId(), $product_id)->abortUnSuccessful();

        return $this->productTagService->storeProductTag(new StoreProductTagData(
            $this->blogId(),
            $product_id,
            $request->tag_names
        ));
    }
}
