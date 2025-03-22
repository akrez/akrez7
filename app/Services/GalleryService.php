<?php

namespace App\Services;

use App\Data\Gallery\IndexGalleryData;
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

    public function getLatestGalleries(int $blogId, IndexGalleryData $indexGalleryData)
    {
        $responseBuilder = ResponseBuilder::new()->input($indexGalleryData);

        $validation = $indexGalleryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $galleries = $this->getLatestGalleriesQuery($blogId, $indexGalleryData->toLongGalleryType(), $indexGalleryData->gallery_id, $indexGalleryData->gallery_category)->get();

        return ResponseBuilder::new()->data([
            'galleries' => (new GalleryCollection($galleries))->toArray(request()),
        ]);
    }

    public function storeGallery(int $blogId, StoreGalleryData $storeGalleryData)
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
            'blog_id' => $blogId,
            'gallery_order' => $storeGalleryData->gallery_order,
            'selected_at' => $selectedAt,
            'ext' => $ext,
            'name' => $name,
            'gallery_category' => $storeGalleryData->gallery_category,
            'gallery_type' => $storeGalleryData->toLongGalleryType(),
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

        $this->resetSelected($blogId, $gallery);

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

    public function updateGallery(int $blogId, int $id, UpdateGalleryData $updateGalleryData)
    {
        $responseBuilder = ResponseBuilder::new()->input($updateGalleryData);

        $gallery = Gallery::query()->where('id', $id)->where('blog_id', $blogId)->first();
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

        $this->resetSelected($blogId, $gallery);

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

    protected function getLatestGalleriesQuery(int $blogId, string $longGalleryType, string $galleryId, string $galleryCategory): Builder
    {
        return Gallery::query()
            ->where('blog_id', $blogId)
            ->where('gallery_type', $longGalleryType)
            ->where('gallery_id', $galleryId)
            ->where('gallery_category', $galleryCategory)
            ->defaultOrder();
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
        $shouldSelect = $this->getLatestGalleriesQuery(
            $blogId,
            $gallery->gallery_type,
            $gallery->gallery_id,
            $gallery->gallery_category->value
        )->first();

        if (! $shouldSelect) {
            return;
        }

        if (empty($shouldSelect->selected_at)) {
            $shouldSelect->selected_at = now()->format('Y-m-d H:i:s.u');
            $shouldSelect->save();
        }

        $shouldNotSelects = $this->getLatestGalleriesQuery(
            $blogId,
            $gallery->gallery_type,
            $gallery->gallery_id,
            $gallery->gallery_category->value
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
