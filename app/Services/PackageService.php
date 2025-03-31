<?php

namespace App\Services;

use App\Data\Package\StorePackageData;
use App\Data\Package\UpdatePackageData;
use App\Http\Resources\Package\PackageCollection;
use App\Http\Resources\Package\PackageResource;
use App\Models\Package;
use App\Support\ResponseBuilder;

class PackageService
{
    public static function new()
    {
        return app(self::class);
    }

    protected function getQuery($blogId)
    {
        return Package::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestPackages(int $blogId)
    {
        $packages = $this->getQuery($blogId)->get();

        return ResponseBuilder::new()->data([
            'packages' => (new PackageCollection($packages))->toArray(request()),
        ]);
    }

    public function storePackage(StorePackageData $storePackageData)
    {
        $responseBuilder = ResponseBuilder::new()->input($storePackageData);

        $validation = $storePackageData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $package = Package::create([
            'package_status' => $storePackageData->package_status,
            'price' => $storePackageData->price,
            'color_id' => $storePackageData->color_id,
            'blog_id' => $storePackageData->blog_id,
            'product_id' => $storePackageData->product_id,
            'guaranty' => $storePackageData->guaranty,
            'description' => $storePackageData->description,
        ]);
        if (! $package) {
            return $responseBuilder->status(500);
        }

        return $responseBuilder->status(201)->data($package)->message(__(':name is created successfully', [
            'name' => __('Package'),
        ]));
    }

    public function getPackage(int $blogId, int $id)
    {
        $responseBuilder = ResponseBuilder::new();

        $package = $this->getQuery($blogId)->where('id', $id)->first();
        if (! $package) {
            return $responseBuilder->status(404);
        }

        return ResponseBuilder::new()->data([
            'package' => (new PackageResource($package))->toArr(request()),
        ]);
    }

    public function updatePackage(UpdatePackageData $updatePackageData)
    {
        $responseBuilder = ResponseBuilder::new()->input($updatePackageData);

        $validation = $updatePackageData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $package = $this->getQuery($updatePackageData->blog_id)->where('id', $updatePackageData->id)->first();
        if (! $package) {
            return $responseBuilder->status(404);
        }

        $package->update([
            'package_status' => $updatePackageData->package_status,
            'price' => $updatePackageData->price,
            'color_id' => $updatePackageData->color_id,
            'guaranty' => $updatePackageData->guaranty,
            'description' => $updatePackageData->description,
        ]);
        if (! $package->save()) {
            return $responseBuilder->status(500);
        }

        return $responseBuilder
            ->status(201)
            ->data(['package' => (new PackageResource($package))->toArr(request())])
            ->message(__(':name is updated successfully', [
                'name' => $package->name,
            ]));
    }

    public function destroyPackage(int $blogId, int $id)
    {
        $responseBuilder = ResponseBuilder::new();

        $package = $this->getQuery($blogId)->where('id', $id)->first();
        if (! $package) {
            return $responseBuilder->status(404);
        }

        if (! $package->delete()) {
            return $responseBuilder->status(500);
        }

        return ResponseBuilder::new(200)->message(__(':name is deleted successfully', [
            'name' => __('Package'),
        ]));
    }
}
