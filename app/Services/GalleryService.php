<?php

namespace App\Services;

use App\Data\Gallery\IndexCategoryGalleryData;
use App\Data\Gallery\IndexModelGalleryData;
use App\Data\Gallery\StoreGalleryData;
use App\Data\Gallery\UpdateGalleryData;
use App\Http\Resources\Gallery\GalleryCollection;
use App\Http\Resources\Gallery\GalleryResource;
use App\Models\Gallery;
use App\Support\ResponseBuilder;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;

class GalleryService
{
    public static function new()
    {
        return app(self::class);
    }

    public function getLatestCategoryGalleries(IndexCategoryGalleryData $indexCategoryGalleryData)
    {
        $responseBuilder = ResponseBuilder::new()->input($indexCategoryGalleryData);

        $validation = $indexCategoryGalleryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $galleries = $this->getLatestCategoryGalleriesQuery(
            $indexCategoryGalleryData->blog_id,
            $indexCategoryGalleryData->gallery_category
        )->get();

        return ResponseBuilder::new()->data([
            'galleries' => (new GalleryCollection($galleries))->toArray(request()),
        ]);
    }

    public function getLatestModelGalleries(IndexModelGalleryData $indexModelGalleryData)
    {
        $responseBuilder = ResponseBuilder::new()->input($indexModelGalleryData);

        $validation = $indexModelGalleryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $galleries = $this->getLatestModelGalleriesQuery(
            $indexModelGalleryData->blog_id,
            $indexModelGalleryData->gallery_category,
            $indexModelGalleryData->toGalleryType(),
            $indexModelGalleryData->gallery_id
        )->get();

        return ResponseBuilder::new()->data([
            'galleries' => (new GalleryCollection($galleries))->toArray(request()),
        ]);
    }

    public function storeGallery(StoreGalleryData $storeGalleryData)
    {
        $responseBuilder = ResponseBuilder::new()->input($storeGalleryData);

        $validation = $storeGalleryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $realPath = $storeGalleryData->file->getRealPath();

        $ext = $storeGalleryData->file->extension();
        $name = hash_file('sha1', $realPath).'.'.$ext;
        $selectedAt = ($storeGalleryData->is_selected ? now()->format('Y-m-d H:i:s.u') : null);

        $gallery = new Gallery([
            'blog_id' => $storeGalleryData->blog_id,
            'gallery_order' => $storeGalleryData->gallery_order,
            'selected_at' => $selectedAt,
            'ext' => $ext,
            'name' => $name,
            'gallery_category' => $storeGalleryData->gallery_category,
            'gallery_type' => $storeGalleryData->toGalleryType(),
            'gallery_id' => $storeGalleryData->gallery_id,
        ]);
        if (! $gallery->save()) {
            return $responseBuilder->status(500);
        }
        //
        $manager = new ImageManager(Driver::class);
        $image = $manager->read($realPath);
        //
        $uploadResponse = $this->upload(
            $image,
            $gallery->gallery_category->value,
            $gallery->name,
            AutoEncoder::DEFAULT_QUALITY
        );
        if (! $uploadResponse->isSuccessful()) {
            return $responseBuilder
                ->status($uploadResponse->getStatus())
                ->message($uploadResponse->getMessage());
        }

        $this->resetSelected($storeGalleryData->blog_id, $gallery);

        return $responseBuilder->status(201)->data($gallery)->message(__(':name is created successfully', [
            'name' => $gallery->gallery_category->trans(),
        ]));
    }

    public function destroyGallery(int $blogId, int $id)
    {
        $responseBuilder = ResponseBuilder::new();

        $gallery = Gallery::query()->where('id', $id)->where('blog_id', $blogId)->first();
        if (! $gallery) {
            return $responseBuilder->status(404);
        }

        $path = $this->getUri($gallery->gallery_category->value, $gallery->name);

        if (! $gallery->delete()) {
            return ResponseBuilder::new(500)->message('Internal Server Error');
        }

        $sameNameGallery = Gallery::query()
            ->where('name', $gallery->name)
            ->where('gallery_category', $gallery->gallery_category->value)
            ->first();
        if (! $sameNameGallery) {
            Storage::delete($path);
        }

        $this->resetSelected($blogId, $gallery);

        return ResponseBuilder::new(200)->message(__(':name is deleted successfully', [
            'name' => $gallery->gallery_category->trans(),
        ]));
    }

    public function getGallery(int $blogId, int $id)
    {
        $responseBuilder = ResponseBuilder::new();

        $gallery = Gallery::query()->where('id', $id)->where('blog_id', $blogId)->first();
        if (! $gallery) {
            return $responseBuilder->status(404);
        }

        return ResponseBuilder::new()->data([
            'gallery' => (new GalleryResource($gallery))->toArr(request()),
        ]);
    }

    public function updateGallery(UpdateGalleryData $updateGalleryData)
    {
        $responseBuilder = ResponseBuilder::new()->input($updateGalleryData);

        $gallery = Gallery::query()->where('id', $updateGalleryData->id)->where('blog_id', $updateGalleryData->blog_id)->first();
        if (! $gallery) {
            return $responseBuilder->status(404);
        }

        $validation = $updateGalleryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $selectedAt = ($updateGalleryData->is_selected ? now()->format('Y-m-d H:i:s.u') : null);

        $gallery->update([
            'gallery_order' => $updateGalleryData->gallery_order,
            'selected_at' => $selectedAt,
        ]);
        if (! $gallery->save()) {
            return $responseBuilder->status(500);
        }

        $this->resetSelected($updateGalleryData->blog_id, $gallery);

        return $responseBuilder
            ->status(201)
            ->data(['gallery' => (new GalleryResource($gallery))->toArr(request())])
            ->message(__(':name is updated successfully', [
                'name' => $gallery->gallery_category->trans(),
            ]));
    }

    public function getUrlByModel(Gallery $gallery, $whmq = null)
    {
        return $this->getUrl(
            $gallery->gallery_category->value,
            $gallery->name,
            $whmq
        );
    }

    public function getBaseUrl($category)
    {
        return $this->getStorageUrl($this->getUri($category));
    }

    protected function getLatestGalleriesQuery(int $blogId): Builder
    {
        return Gallery::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    protected function getLatestCategoryGalleriesQuery(int $blogId, string $galleryCategory): Builder
    {
        return $this->getLatestGalleriesQuery($blogId)
            ->where('gallery_category', $galleryCategory);
    }

    protected function getLatestModelGalleriesQuery(int $blogId, string $galleryCategory, string $galleryType, string $galleryId): Builder
    {
        return $this->getLatestCategoryGalleriesQuery($blogId, $galleryCategory)
            ->where('gallery_id', $galleryId)
            ->where('gallery_type', $galleryType);
    }

    protected function upload($image, $category, $name, $quality, $whmq = null)
    {
        try {
            $path = $this->getUri($category, $name, $whmq);
            //
            $isUploaded = Storage::put($path, $image->encode(new AutoEncoder(quality: intval($quality))));
            if ($isUploaded) {
                $pathinfo = pathinfo($path);

                return ResponseBuilder::new(201)->data([
                    'width' => $image->width(),
                    'height' => $image->height(),
                    'name' => $pathinfo['basename'],
                    'path' => $path,
                    'url' => $this->getUrl($category, $name, $whmq),
                ]);
            }
        } catch (Exception $e) {
        }

        return ResponseBuilder::new(500);
    }

    protected function resetSelected(int $blogId, Gallery $gallery)
    {
        $shouldSelect = $this->getLatestModelGalleriesQuery(
            $blogId,
            $gallery->gallery_category->value,
            $gallery->gallery_type,
            $gallery->gallery_id
        )->first();

        if (! $shouldSelect) {
            return;
        }

        if (empty($shouldSelect->selected_at)) {
            $shouldSelect->selected_at = now()->format('Y-m-d H:i:s.u');
            $shouldSelect->save();
        }

        $shouldNotSelects = $this->getLatestModelGalleriesQuery(
            $blogId,
            $gallery->gallery_category->value,
            $gallery->gallery_type,
            $gallery->gallery_id
        )->whereNotNull('selected_at')->where('id', '<>', $shouldSelect->id)->get();

        foreach ($shouldNotSelects as $shouldNotSelect) {
            $shouldNotSelect->selected_at = null;
            $shouldNotSelect->save();
        }
    }

    protected function getUrl($category, $name, $whmq = null)
    {
        return $this->getStorageUrl($this->getUri($category, $name, $whmq));
    }

    protected function getStorageUrl($url)
    {
        return Storage::url($url);
    }

    protected function getUri($category, $name = null, $whmq = null)
    {
        $segments = [
            'gallery',
            $category,
        ];
        if ($whmq) {
            $segments[] = $whmq;
        }
        if ($name) {
            $segments[] = $name;
        }

        return implode('/', $segments);
    }
}
