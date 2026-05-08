<?php

namespace App\Http\Controllers;

use App\Data\Package\StorePackageData;
use App\Data\Package\UpdatePackageData;
use App\Services\ColorService;
use App\Services\PackageService;
use App\Services\ProductService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function __construct(
        protected ColorService $colorService,
        protected ProductService $productService,
        protected PackageService $packageService
    ) {}

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        return view('package.index');
    }

    public function list(Request $request)
    {
        $productsResponse = $this->productService->getLatestProducts($this->blogId());

        $response = $this->packageService->getLatestPackages($this->blogId());

        return ApiResponse::new()->data([
            'packages' => $response->getData('packages'),
            'products' => $productsResponse->getData('products'),
            'colors' => $this->getColors($this->blogId()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $blogId = $this->blogId();

        $productId = intval($request->product_id);
        if (! $productId) {
            return ApiResponse::new(404);
        }

        $this->productService->getProduct($blogId, $productId)->abortUnSuccessful();

        $storePackageData = new StorePackageData(
            null,
            $productId,
            $blogId,
            $request->price,
            $request->package_status,
            $request->color_id,
            $request->guaranty,
            $request->unit,
            $request->show_price,
            $request->description
        );

        return $this->packageService->storePackage($storePackageData);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $blogId = $this->blogId();

        $productId = intval($request->product_id);
        if (! $productId) {
            return ApiResponse::new(404);
        }

        $this->productService->getProduct($blogId, $productId)->abortUnSuccessful();

        $updatePackageData = new UpdatePackageData(
            $id,
            $productId,
            $blogId,
            $request->package_status,
            $request->price,
            $request->show_price,
        );

        return $this->packageService->updatePackage($updatePackageData);
    }

    public function destroy(Request $request, int $id)
    {
        return $this->packageService->destroyPackage($this->blogId(), $id);
    }

    protected function getColors(int $blogId)
    {
        return ColorService::new()->getLatestColors($blogId)->getData('colors');
    }
}
