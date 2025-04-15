<?php

namespace App\Services;

use App\Data\Gallery\EffectGalleryData;
use App\Data\Gallery\IndexCategoryGalleryData;
use App\Data\Gallery\IndexModelGalleryData;
use App\Data\Gallery\StoreGalleryData;
use App\Data\Gallery\UpdateGalleryData;
use App\Http\Resources\Gallery\GalleryCollection;
use App\Http\Resources\Gallery\GalleryResource;
use App\Models\Gallery;
use App\Support\ApiResponse;
use App\Support\WebResponse;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\AutoEncoder;
use Intervention\Image\ImageManager;

class GalleryService extends Service
{
    const MODE_CONTAIN = 'contain';

    const VALID_MODES = [self::MODE_CONTAIN];

    const WHMQ_REGEX_PATTERN = '/[A-Za-z0-9\-\.\_]/';

    const MAX_SIZE = 3096;

    public static function new()
    {
        return app(self::class);
    }

    public function getApiResource(int $blogId, int $id): ApiResponse
    {
        $model = $this->getLatestApiQuery($blogId)
            ->first();

        return ApiResponse::new(200)->data([
            'gallery' => (new GalleryResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'galleries' => (new GalleryCollection($models))->toArr(),
        ]);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return Gallery::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestCategoryGalleries(IndexCategoryGalleryData $indexCategoryGalleryData)
    {
        $webResponse = WebResponse::new()->input($indexCategoryGalleryData);

        $validation = $indexCategoryGalleryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $galleries = $this->getLatestCategoryBlogQuery(
            $indexCategoryGalleryData->blog_id,
            $indexCategoryGalleryData->gallery_category
        )->get();

        return WebResponse::new()->data([
            'galleries' => (new GalleryCollection($galleries))->toArr(),
        ]);
    }

    public function getLatestModelGalleries(IndexModelGalleryData $indexModelGalleryData)
    {
        $webResponse = WebResponse::new()->input($indexModelGalleryData);

        $validation = $indexModelGalleryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $galleries = $this->getLatestModelBlogQuery(
            $indexModelGalleryData->blog_id,
            $indexModelGalleryData->gallery_category,
            $indexModelGalleryData->toGalleryType(),
            $indexModelGalleryData->gallery_id
        )->get();

        return WebResponse::new()->data([
            'galleries' => (new GalleryCollection($galleries))->toArr(),
        ]);
    }

    public function storeGallery(StoreGalleryData $storeGalleryData)
    {
        $webResponse = WebResponse::new()->input($storeGalleryData);

        $validation = $storeGalleryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
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
            return $webResponse->status(500);
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
            return $webResponse
                ->status($uploadResponse->getStatus())
                ->message($uploadResponse->getMessage());
        }

        $this->resetSelected($storeGalleryData->blog_id, $gallery);

        return $webResponse->status(201)->data($gallery)->message(__(':name is created successfully', [
            'name' => $gallery->gallery_category->trans(),
        ]));
    }

    public function destroyGallery(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $gallery = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $gallery) {
            return $webResponse->status(404);
        }

        $path = $this->getUri($gallery->gallery_category->value, $gallery->name);

        if (! $gallery->delete()) {
            return WebResponse::new(500)->message('Internal Server Error');
        }

        $sameNameGallery = Gallery::query()
            ->where('name', $gallery->name)
            ->where('gallery_category', $gallery->gallery_category->value)
            ->first();
        if (! $sameNameGallery) {
            Storage::delete($path);
        }

        $this->resetSelected($blogId, $gallery);

        return WebResponse::new(200)->message(__(':name is deleted successfully', [
            'name' => $gallery->gallery_category->trans(),
        ]));
    }

    public function effect(EffectGalleryData $effectGalleryData)
    {
        $webResponse = WebResponse::new()->input($effectGalleryData);

        $validation = $effectGalleryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $gallery = Gallery::query()
            ->where('gallery_category', $effectGalleryData->gallery_category)
            ->where('name', $effectGalleryData->name)
            ->first();
        if (! $gallery) {
            return $webResponse->status(404);
        }

        $sourceFilePath = $this->getUri(
            $effectGalleryData->gallery_category,
            $effectGalleryData->name,
        );
        //
        $manager = new ImageManager(Driver::class);
        $image = $manager->read($this->getStoragePath($sourceFilePath));
        //
        $width = $effectGalleryData->getWidth();
        $height = $effectGalleryData->getHeight();
        //
        if ($width && $height) {
        } elseif ($width) {
            $height = ($width * $image->height()) / $image->width();
        } elseif ($height) {
            $width = ($height * $image->width()) / $image->height();
        } else {
            $width = $image->width();
            $height = $image->height();
        }
        //
        if ($effectGalleryData->getMode() === self::MODE_CONTAIN) {
            $width = $height = max($width, $height);
            $image->contain(width: $width, height: $height, background: $image->pickColor(0, 0));
        } else {
            $image->resize(width: $width, height: $height);
        }

        $uploadResponse = $this->upload(
            $image,
            $effectGalleryData->gallery_category,
            $effectGalleryData->name,
            $effectGalleryData->getQuality(),
            $effectGalleryData->whmq
        );

        if (! $uploadResponse->isSuccessful()) {
            return $webResponse
                ->status($uploadResponse->getStatus())
                ->message($uploadResponse->getMessage());
        }

        return $webResponse->data([
            'name' => $effectGalleryData->name,
            'path' => $this->getUri(
                $effectGalleryData->gallery_category,
                $effectGalleryData->name,
                $effectGalleryData->whmq
            ),
        ]);
    }

    public function getGallery(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $gallery = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $gallery) {
            return $webResponse->status(404);
        }

        return WebResponse::new()->data([
            'gallery' => (new GalleryResource($gallery))->toArr(),
        ]);
    }

    public function updateGallery(UpdateGalleryData $updateGalleryData)
    {
        $webResponse = WebResponse::new()->input($updateGalleryData);

        $gallery = $this->getLatestBlogQuery($updateGalleryData->blog_id)->where('id', $updateGalleryData->id)->first();
        if (! $gallery) {
            return $webResponse->status(404);
        }

        $validation = $updateGalleryData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $selectedAt = ($updateGalleryData->is_selected ? now()->format('Y-m-d H:i:s.u') : null);

        $gallery->update([
            'gallery_order' => $updateGalleryData->gallery_order,
            'selected_at' => $selectedAt,
        ]);
        if (! $gallery->save()) {
            return $webResponse->status(500);
        }

        $this->resetSelected($updateGalleryData->blog_id, $gallery);

        return $webResponse
            ->status(201)
            ->data(['gallery' => (new GalleryResource($gallery))->toArr()])
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

    protected function getLatestCategoryBlogQuery(int $blogId, string $galleryCategory): Builder
    {
        return $this->getLatestBlogQuery($blogId)
            ->where('gallery_category', $galleryCategory);
    }

    protected function getLatestModelBlogQuery(int $blogId, string $galleryCategory, string $galleryType, string $galleryId): Builder
    {
        return $this->getLatestCategoryBlogQuery($blogId, $galleryCategory)
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

                return WebResponse::new(201)->data([
                    'width' => $image->width(),
                    'height' => $image->height(),
                    'name' => $pathinfo['basename'],
                    'path' => $path,
                    'url' => $this->getUrl($category, $name, $whmq),
                ]);
            }
        } catch (Exception $e) {
        }

        return WebResponse::new(500);
    }

    protected function resetSelected(int $blogId, Gallery $gallery)
    {
        $shouldSelect = $this->getLatestModelBlogQuery(
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

        $shouldNotSelects = $this->getLatestModelBlogQuery(
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

    protected function getStoragePath($path)
    {
        return Storage::path($path);
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
