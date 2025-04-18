<?php

namespace App\Http\Controllers;

use App\Data\Package\StorePackageData;
use App\Data\Package\UpdatePackageData;
use App\Services\ColorService;
use App\Services\PackageService;
use App\Services\ProductService;
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
    public function index(Request $request, int $product_id)
    {
        $productResponse = $this->productService->getProduct($this->blogId(), $product_id)->abortUnSuccessful();

        $response = $this->packageService->getLatestPackages($this->blogId(), $product_id);

        return view('package.index', [
            'packages' => $response->getData('packages'),
            'product' => $productResponse->getData('product'),
            'colorsIdArray' => $this->getColorsIdArray($this->blogId()),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request, int $product_id)
    {
        $productResponse = $this->productService->getProduct($this->blogId(), $product_id)->abortUnSuccessful();

        return view('package.create', [
            'product' => $productResponse->getData('product'),
            'colorsIdArray' => $this->getColorsIdArray($this->blogId()),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request, int $product_id)
    {
        $productResponse = $this->productService->getProduct($this->blogId(), $product_id)->abortUnSuccessful();

        $storePackageData = new StorePackageData(
            null,
            $product_id,
            $this->blogId(),
            $request->price,
            $request->package_status,
            $request->color_id,
            $request->guaranty,
            $request->description
        );

        $response = $this->packageService->storePackage($storePackageData);

        return $response->successfulRoute(route('products.packages.index', ['product_id' => $product_id]));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, int $product_id, int $id)
    {
        $productResponse = $this->productService->getProduct($this->blogId(), $product_id)->abortUnSuccessful();

        $response = $this->packageService->getPackage($this->blogId(), $id)->abortUnSuccessful();

        return view('package.edit', [
            'package' => $response->getData('package'),
            'product' => $productResponse->getData('product'),
            'colorsIdArray' => $this->getColorsIdArray($this->blogId()),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $product_id, int $id)
    {
        $productResponse = $this->productService->getProduct($this->blogId(), $product_id)->abortUnSuccessful();

        $updatePackageData = new UpdatePackageData(
            $id,
            $product_id,
            $this->blogId(),
            $request->package_status
        );

        $response = $this->packageService->updatePackage($updatePackageData);

        return $response->successfulRoute(route('products.packages.index', ['product_id' => $product_id]));
    }

    public function destroy(Request $request, int $product_id, int $id)
    {
        return $this->packageService->destroyPackage($this->blogId(), $id);
    }

    public function getColorsIdArray(int $blogId)
    {
        $array = ColorService::new()->getLatestColors($blogId)->getData('colors');

        return collect($array)->pluck(null, 'id')->toArray();
    }
}
