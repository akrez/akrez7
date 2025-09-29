<?php

namespace App\Services;

use App\Data\Package\StorePackageData;
use App\Data\Package\UpdatePackageData;
use App\Enums\PackageStatusEnum;
use App\Http\Resources\Package\PackageCollection;
use App\Http\Resources\Package\PackageResource;
use App\Models\Package;
use App\Support\ApiResponse;
use App\Support\WebResponse;

class PackageService extends Service
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
            'package' => (new PackageResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'packages' => (new PackageCollection($models))->toArr(),
        ]);
    }

    protected function getLatestApiQuery($blogId)
    {
        return $this->getLatestBaseQuery($blogId)
            ->where('package_status', '!=', PackageStatusEnum::DEACTIVE->value);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return Package::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestPackages(int $blogId, int $productId)
    {
        $packages = $this->getLatestBlogQuery($blogId)->where('product_id', $productId)->get();

        return WebResponse::new()->data([
            'packages' => (new PackageCollection($packages))->toArr(),
        ]);
    }

    public function getLatestPackagesWithTrashedByIds(int $blogId, array $ids)
    {
        $packages = $this->getLatestBlogQuery($blogId)->withTrashed()->whereIn('id', $ids)->get();

        return WebResponse::new()->data([
            'packages' => (new PackageCollection($packages))->toArr(),
        ]);
    }

    public function storePackage(StorePackageData $storePackageData)
    {
        $webResponse = WebResponse::new()->input($storePackageData);

        $validation = $storePackageData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $package = Package::create([
            'package_status' => $storePackageData->package_status,
            'price' => $storePackageData->price,
            'color_id' => $storePackageData->color_id,
            'blog_id' => $storePackageData->blog_id,
            'product_id' => $storePackageData->product_id,
            'guaranty' => $storePackageData->guaranty,
            'unit' => $storePackageData->unit,
            'show_price' => $storePackageData->show_price,
            'description' => $storePackageData->description,
        ]);
        if (! $package) {
            return $webResponse->status(500);
        }

        return $webResponse->status(201)->data($package)->message(__(':name is created successfully', [
            'name' => __('Package'),
        ]));
    }

    public function getPackage(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $package = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $package) {
            return $webResponse->status(404);
        }

        return WebResponse::new()->data([
            'package' => (new PackageResource($package))->toArr(),
        ]);
    }

    public function updatePackage(UpdatePackageData $updatePackageData)
    {
        $webResponse = WebResponse::new()->input($updatePackageData);

        $validation = $updatePackageData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $package = $this->getLatestBlogQuery($updatePackageData->blog_id)->where('id', $updatePackageData->id)->first();
        if (! $package) {
            return $webResponse->status(404);
        }

        $package->update([
            'package_status' => $updatePackageData->package_status,
            'price' => $updatePackageData->price,
            'show_price' => $updatePackageData->show_price,
        ]);
        if (! $package->save()) {
            return $webResponse->status(500);
        }

        return $webResponse
            ->status(201)
            ->data(['package' => (new PackageResource($package))->toArr()])
            ->message(__(':name is updated successfully', [
                'name' => $package->name,
            ]));
    }

    public function destroyPackage(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $package = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $package) {
            return $webResponse->status(404);
        }

        if (! $package->delete()) {
            return $webResponse->status(500);
        }

        return WebResponse::new(200)->message(__(':name is deleted successfully', [
            'name' => __('Package'),
        ]));
    }
}
