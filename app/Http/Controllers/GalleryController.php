<?php

namespace App\Http\Controllers;

use App\Data\Gallery\IndexGalleryData;
use App\Data\Gallery\StoreGalleryData;
use App\Data\Gallery\UpdateGalleryData;
use App\Services\GalleryService;
use App\Support\ResponseBuilder;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function __construct(protected GalleryService $galleryService) {}

    public function index(string $gallery_category, string $gallery_type, string $gallery_id)
    {
        $response = $this->galleryService->getLatestGalleries(
            app('ActiveBlog')->id(),
            new IndexGalleryData(
                $gallery_category,
                $gallery_type,
                $gallery_id,
            )
        );
        if (! $response->isSuccessful()) {
            return ResponseBuilder::new($response->getStatus());
        }

        return view('gallery.index', [
            'galleries' => $response->getData('galleries'),
            'gallery_category' => $gallery_category,
            'gallery_type' => $gallery_type,
            'gallery_id' => $gallery_id,
        ]);
    }

    public function store(Request $request)
    {
        $response = $this->galleryService->storeGallery(
            app('ActiveBlog')->id(),
            new StoreGalleryData(
                $request->file('file'),
                $request->gallery_category,
                $request->gallery_type,
                $request->gallery_id,
                $request->gallery_order,
                $request->is_selected,
            )
        );

        return $response;
    }

    public function edit(Request $request, int $id)
    {
        $response = $this->galleryService->getGallery(app('ActiveBlog')->id(), $id);

        return view('gallery.edit', [
            'gallery' => $response->getData('gallery'),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $response = $this->galleryService->updateGallery(
            app('ActiveBlog')->id(),
            $id,
            new UpdateGalleryData(
                $request->gallery_order,
                $request->is_selected,
            )
        );

        $gallery = $response->getData('gallery');
        if ($gallery) {
            $successfulRoute = route('galleries.index', [
                'gallery_category' => $gallery['gallery_category']['value'],
                'gallery_type' => $gallery['long_gallery_type'],
                'gallery_id' => $gallery['gallery_id'],
            ]);
        } else {
            $successfulRoute = null;
        }

        return $response->successfulRoute($successfulRoute);
    }

    public function destroy(Request $request, int $id)
    {
        $response = $this->galleryService->destroyGallery(
            app('ActiveBlog')->id(),
            $id
        );

        return $response;
    }
}
