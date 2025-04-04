<?php

namespace App\Http\Controllers\Api;

use App\Enums\GalleryCategoryEnum;
use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\Product;
use App\Services\BlogService;
use App\Services\ColorService;
use App\Services\ContactService;
use App\Services\GalleryService;
use App\Services\PackageService;
use App\Services\ProductPropertyService;
use App\Services\ProductService;
use App\Services\ProductTagService;
use App\Support\ApiResponse;
use App\Support\Arr;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function __construct(protected BlogService $blogService) {}

    public function index(Request $request, int $id)
    {
        $blogResponse = $this->blogService->getApiResource($id)->abortUnSuccessful();

        $blog = $blogResponse->getData('blog');

        $contacts = ContactService::new()->getApiCollection($id)->getData('contacts');

        $colors = ColorService::new()->getApiCollection($id)->getData('colors');
        $organizedColors = [];
        foreach ($colors as $color) {
            $colorItem = $color;
            unset($colorItem['id']);
            $organizedColors[$color['id']] = $colorItem;
        }

        $packages = PackageService::new()->getApiCollection($id)->getData('packages');
        $organizedPackages = [];
        foreach ($packages as $package) {
            $packageItem = $package;
            $packageItem['color'] = Arr::get($organizedColors, $package['color_id']);
            unset($colorItem['color_id']);
            $organizedPackages[$package['product_id']][] = $packageItem;
        }

        $productProperties = ProductPropertyService::new()->getApiCollection($id)->getData('product_properties');
        $organizedProductProperties = [];
        foreach ($productProperties as $productProperty) {
            if (! isset($organizedProductProperties[$productProperty['product_id']][$productProperty['property_key']])) {
                $organizedProductProperties[$productProperty['product_id']][$productProperty['property_key']] = [
                    'property_key' => $productProperty['property_key'],
                    'property_values' => [],
                ];
            }
            $organizedProductProperties[$productProperty['product_id']][$productProperty['property_key']]['property_values'][] = $productProperty['property_value'];
        }

        $productTags = ProductTagService::new()->getApiCollection($id)->getData('product_tags');
        $organizedProductTags = [];
        foreach ($productTags as $productTag) {
            $organizedProductTags[$productTag['product_id']][] = $productTag['tag_name'];
        }

        $galleries = GalleryService::new()->getApiCollection($id)->getData('galleries');
        $organizedGalleries = [];
        foreach ($galleries as $gallery) {
            $organizedGalleries[$gallery['gallery_category']['value']][$gallery['gallery_type']][$gallery['gallery_id']][] = [
                'name' => $gallery['name'],
                'base_url' => $gallery['base_url'],
                'url' => $gallery['url'],
                'contain_url' => $gallery['contain_url'],
            ];
        }

        foreach (GalleryCategoryEnum::values() as $galleryCategoryEnumValue) {
            $blog['galleries'][$galleryCategoryEnumValue] = Arr::get($organizedGalleries, $galleryCategoryEnumValue.'.'.Blog::class.'.'.$id.'.'. 0);
        }

        $products = ProductService::new()->getApiCollection($id)->getData('products');
        $organizedProducts = [];
        foreach ($products as $product) {
            $productItem = $product;
            $productItem['packages'] = array_values(Arr::get($organizedPackages, $product['id'], []));
            $productItem['product_properties'] = array_values(Arr::get($organizedProductProperties, $product['id'], []));
            $productItem['product_tags'] = array_values(Arr::get($organizedProductTags, $product['id'], []));
            //
            foreach (GalleryCategoryEnum::values() as $galleryCategoryEnumValue) {
                $productItem['galleries'][$galleryCategoryEnumValue] = Arr::get($organizedGalleries, $galleryCategoryEnumValue.'.'.Product::class.'.'.$product['id']);
            }
            //
            unset($productItem['product_status']);
            unset($productItem['created_at']);
            unset($productItem['updated_at']);
            //
            $organizedProducts[] = $productItem;
        }

        return ApiResponse::new()->data([
            'blog' => $blog,
            'contacts' => $contacts,
            'products' => $organizedProducts,
        ]);
    }
}
