<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\BlogService;
use App\Services\ColorService;
use App\Services\ContactService;
use App\Services\GalleryService;
use App\Services\PackageService;
use App\Services\ProductPropertyService;
use App\Services\ProductService;
use App\Services\ProductTagService;
use App\Support\ApiResponse;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(protected BlogService $blogService) {}

    public function index(Request $request, int $id)
    {
        $blogResponse = $this->blogService->getApiResource($id)->abortUnSuccessful();

        return ApiResponse::new()->data([
            'blog' => $blogResponse->getData('blog'),
            'products' => ProductService::new()->getApiCollection($id)->getData('products'),
            'colors' => ColorService::new()->getApiCollection($id)->getData('colors'),
            'contacts' => ContactService::new()->getApiCollection($id)->getData('contacts'),
            'packages' => PackageService::new()->getApiCollection($id)->getData('packages'),
            'product_properties' => ProductPropertyService::new()->getApiCollection($id)->getData('product_properties'),
            'product_tags' => ProductTagService::new()->getApiCollection($id)->getData('product_tags'),
            'galleries' => GalleryService::new()->getApiCollection($id)->getData('galleries'),
        ]);
    }
}
