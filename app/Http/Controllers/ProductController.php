<?php

namespace App\Http\Controllers;

use App\Data\Gallery\IndexCategoryGalleryData;
use App\Data\Product\StoreProductData;
use App\Data\Product\UpdateProductData;
use App\Enums\GalleryCategoryEnum;
use App\Services\CategoryService;
use App\Services\GalleryService;
use App\Services\ProductService;
use App\Support\WebResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function __construct(
        protected CategoryService $categoryService,
        protected GalleryService $galleryService,
        protected ProductService $productService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $latestProductsResponse = $this->productService->getLatestProducts($this->blogId())->abortUnSuccessful();

        $latestCategoryGalleriesResponse = $this->galleryService->getLatestCategoryGalleries(
            new IndexCategoryGalleryData(
                $this->blogId(),
                GalleryCategoryEnum::PRODUCT_IMAGE->value
            )
        )->abortUnSuccessful();

        return view('product.index', [
            'products' => $latestProductsResponse->getData('products'),
            'categories' => $this->categoryService->getLatestCategories($this->blogId())->getData('categories'),
            'galleries' => [
                GalleryCategoryEnum::PRODUCT_IMAGE->value => $latestCategoryGalleriesResponse->getData('galleries'),
            ],
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('product.create', [
            'categories' => $this->categoryService->getLatestCategories($this->blogId())->getData('categories'),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $blogId = $this->blogId();

        $categoryId = intval($request->category_id);
        if (! $categoryId) {
            return WebResponse::new(404);
        }

        $this->categoryService->getCategory($blogId, $categoryId)->abortUnSuccessful();

        $storeProductData = new StoreProductData(
            null,
            $this->blogId(),
            $request->code,
            $request->name,
            $request->product_status,
            $request->product_order,
            $categoryId
        );

        $response = $this->productService->storeProduct($storeProductData);

        return $response->successfulRoute(route('products.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $response = $this->productService->getProduct($this->blogId(), $id)->abortUnSuccessful();

        return view('product.edit', [
            'product' => $response->getData('product'),
            'categories' => $this->categoryService->getLatestCategories($this->blogId())->getData('categories'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $blogId = $this->blogId();

        $categoryId = intval($request->category_id);
        if (! $categoryId) {
            return WebResponse::new(404);
        }

        $this->categoryService->getCategory($blogId, $categoryId)->abortUnSuccessful();

        $updateProductData = new UpdateProductData(
            $id,
            $this->blogId(),
            $request->code,
            $request->name,
            $request->product_status,
            $request->product_order,
            $categoryId
        );

        $response = $this->productService->updateProduct($updateProductData);

        return $response->successfulRoute(route('products.index'));
    }
}
