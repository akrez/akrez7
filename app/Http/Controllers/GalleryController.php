<?php

namespace App\Http\Controllers;

use App\Data\Gallery\EffectGalleryData;
use App\Data\Gallery\IndexModelGalleryData;
use App\Data\Gallery\StoreGalleryData;
use App\Data\Gallery\UpdateGalleryData;
use App\Services\GalleryService;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function __construct(protected GalleryService $galleryService) {}

    public function effect($gallery_category, $whmq, $name)
    {
        $galleryService = GalleryService::new();

        $response = $galleryService->effect(new EffectGalleryData(
            $gallery_category, $whmq, $name
        ))->abortUnSuccessful();

        return Response()->download(
            $response->getData('path'),
            $response->getData('name'),
            [],
            'inline'
        );
    }

    public function index(string $gallery_category, string $gallery_id)
    {
        $response = $this->galleryService->getLatestModelGalleries(
            new IndexModelGalleryData(
                $this->blogId(),
                $gallery_category,
                $gallery_id,
            )
        )->abortUnSuccessful();

        return view('gallery.index', [
            'galleries' => $response->getData('galleries'),
            'gallery_category' => $gallery_category,
            'gallery_id' => $gallery_id,
        ]);
    }

    public function store(Request $request)
    {
        $response = $this->galleryService->storeGallery(
            new StoreGalleryData(
                $this->blogId(),
                $request->file('file'),
                $request->gallery_category,
                $request->gallery_id,
                $request->gallery_order,
                $request->is_selected,
            )
        );

        return $response;
    }

    public function edit(Request $request, int $id)
    {
        $response = $this->galleryService->getGallery($this->blogId(), $id)->abortUnSuccessful();

        return view('gallery.edit', [
            'gallery' => $response->getData('gallery'),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $response = $this->galleryService->updateGallery(
            new UpdateGalleryData(
                $id,
                $this->blogId(),
                $request->gallery_order,
                $request->is_selected,
            )
        );

        $gallery = $response->getData('gallery');
        if ($gallery) {
            $successfulRoute = route('galleries.index', [
                'gallery_category' => $gallery['gallery_category']['value'],
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
            $this->blogId(),
            $id
        );

        return $response;
    }
}
