<?php

namespace App\Services;

use App\Enums\CacheKeyEnum;
use App\Enums\GalleryCategoryEnum;
use App\Enums\PresenterEnum;
use App\Models\Blog;
use App\Models\Product;
use App\Support\ApiResponse;
use App\Support\Arr;
use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use SimpleXMLElement;

class PresentService
{
    const PRESENT_INFO_REQUEST_KEY = '__present_info';

    public static function new()
    {
        return app(self::class);
    }

    public function makePresentInfo($userId, $presenter, $blogId, ?string $domain): ?array
    {
        if ($presenter === PresenterEnum::DOMAIN->value) {
            if (empty($domain)) {
                return null;
            }
            $blogId = DomainService::new()->domainToBlogId($domain)->getData('blog_id');
        }

        if (empty($blogId)) {
            return null;
        }

        if ($presenter === PresenterEnum::PREVIEW->value) {
            $blogModel = BlogService::new()->getUserBlog($userId, $blogId)->getData('blog');
            if (empty($blogModel)) {
                return null;
            }
            $blog = BlogService::new()->getBlogResource($blogModel)->getData('blog');
        } else {
            $blog = BlogService::new()->getApiResource($blogId)->getData('blog');
        }

        if (empty($blog)) {
            return null;
        }

        return [
            PresentService::PRESENT_INFO_REQUEST_KEY => [
                'presenter' => $presenter,
                'blog_id' => $blogId,
                'domain' => $domain,
            ],
        ];
    }

    public function getRequestPresentInfo(Request $request): array
    {
        return (array) Arr::get($request, PresentService::PRESENT_INFO_REQUEST_KEY);
    }

    /**
     * Generate the URL to a named route.
     *
     * @param  \BackedEnum|string  $name
     * @param  mixed  $parameters
     * @param  bool  $absolute
     */
    public function routeByPresentInfo(array $presentInfo, $name, $parameters = [], $absolute = true)
    {
        if ($presentInfo['presenter'] === PresenterEnum::DOMAIN->name) {
            $parameters['domain'] = $presentInfo['domain'];
        } else {
            $parameters['blog_id'] = $presentInfo['blog_id'];
        }

        return route($presentInfo['presenter'].'.'.$name, $parameters, $absolute);
    }

    public function forgetCachedApiResponse(int $blogId)
    {
        Cache::forget($this->showBlogCacheKey($blogId));
    }

    public function getCachedApiResponse(int $blogId, \Illuminate\Http\Request $request, bool $forgetCache = false): ApiResponse
    {
        if ($forgetCache) {
            $this->forgetCachedApiResponse($blogId);
        }

        $response = Cache::remember($this->showBlogCacheKey($blogId), 3600, function () use ($blogId) {
            return $this->getApiResponse($blogId);
        });

        if ($response->isSuccessful()) {
            PayvoiceService::new()->storePayvoice($blogId, $request);
        }

        return $response;
    }

    public function getSitemapResponse(int $blogId, \Illuminate\Http\Request $request, bool $forgetCache = false): ApiResponse
    {
        $blogResponse = $this->getCachedApiResponse($blogId, $request, $forgetCache);
        if (! $blogResponse->isSuccessful()) {
            return ApiResponse::new($blogResponse->getStatus());
        }

        $presentInfo = $this->getRequestPresentInfo($request);
        $loc = $this->routeByPresentInfo($presentInfo, 'show');

        $sitemap = new SimpleXMLElement(implode('', [
            '<?xml version="1.0" encoding="UTF-8" ?>',
            '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml" xmlns:image="http://www.google.com/schemas/sitemap-image/1.1" xmlns:video="http://www.google.com/schemas/sitemap-video/1.1" xmlns:news="http://www.google.com/schemas/sitemap-news/0.9">',
            '</urlset>',
        ]));

        $xmlurl = $sitemap->addChild('url');
        $xmlurl->addChild('loc', $loc);
        $xmlurl->addChild('lastmod', now()->format(DateTime::ATOM));
        $xmlurl->addChild('changefreq', 'daily');
        $xmlurl->addChild('priority', 1);

        return ApiResponse::new($blogResponse->getStatus())->data([
            'sitemap' => $sitemap->asXML(),
        ]);
    }

    protected function showBlogCacheKey(int $blogId)
    {
        return CacheKeyEnum::PRESENT_BLOG_SHOW->name.':'.$blogId;
    }

    protected function getApiResponse(int $blogId): ApiResponse
    {
        $blogResponse = BlogService::new()->getBlog($blogId);
        if (! $blogResponse->isSuccessful()) {
            return ApiResponse::new($blogResponse->getStatus());
        }

        $raw = [
            'blog' => $blogResponse->getData('blog'),
            'colors' => ColorService::new()->getApiCollection($blogId)->getData('colors'),
            'contacts' => ContactService::new()->getApiCollection($blogId)->getData('contacts'),
            'packages' => PackageService::new()->getApiCollection($blogId)->getData('packages'),
            'products' => ProductService::new()->getApiCollection($blogId)->getData('products'),
            'galleries' => GalleryService::new()->getApiCollection($blogId)->getData('galleries'),
            'productTags' => ProductTagService::new()->getApiCollection($blogId)->getData('product_tags'),
            'productProperties' => ProductPropertyService::new()->getApiCollection($blogId)->getData('product_properties'),
        ];

        $organized = [
            'colors' => [],
            'packages' => [],
            'galleries' => [],
            'productTags' => [],
            'productProperties' => [],
        ];

        foreach ($raw['colors'] as $color) {
            $organized['colors'][$color['id']] = [
                'code' => $color['code'],
                'name' => $color['name'],
            ];
        }

        foreach ($raw['packages'] as $package) {
            $organized['packages'][$package['product_id']][] = [
                'id' => $package['id'],
                'product_id' => $package['product_id'],
                'package_status' => $package['package_status'],
                'price' => $package['price'],
                'guaranty' => $package['guaranty'],
                'unit' => $package['unit'],
                'show_price' => $package['show_price'],
                'description' => $package['description'],
                'color' => Arr::get($organized['colors'], $package['color_id'], []),
            ];
        }

        foreach ($raw['galleries'] as $gallery) {
            $organized['galleries'][$gallery['gallery_category']['value']][$gallery['gallery_type']][$gallery['gallery_id']][] = [
                'name' => $gallery['name'],
                'base_url' => $gallery['base_url'],
                'url' => $gallery['url'],
                'contain_url' => $gallery['contain_url'],
            ];
        }

        foreach ($raw['productTags'] as $productTag) {
            $organized['productTags'][$productTag['product_id']][] = $productTag['tag_name'];
        }

        foreach ($raw['productProperties'] as $productProperty) {
            if (! isset($organized['productProperties'][$productProperty['product_id']][$productProperty['property_key']])) {
                $organized['productProperties'][$productProperty['product_id']][$productProperty['property_key']] = [
                    'property_key' => $productProperty['property_key'],
                    'property_values' => [],
                ];
            }
            $organized['productProperties'][$productProperty['product_id']][$productProperty['property_key']]['property_values'][] = $productProperty['property_value'];
        }

        $output = [
            'blog' => null,
            'contacts' => [],
            'products' => [],
        ];

        $output['blog'] = [
            'id' => $raw['blog']['id'],
            'name' => $raw['blog']['name'],
            'short_description' => $raw['blog']['short_description'],
            'description' => $raw['blog']['description'],
            'galleries' => $this->getOrganizedGalleries($organized['galleries'], Blog::class.'.'.$raw['blog']['id']),
        ];

        foreach ($raw['contacts'] as $contact) {
            $output['contacts'][] = [
                'contact_type' => $contact['contact_type'],
                'contact_key' => $contact['contact_key'],
                'contact_value' => $contact['contact_value'],
                'contact_link' => $contact['contact_link'],
            ];
        }

        foreach ($raw['products'] as $product) {
            $output['products'][] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'code' => $product['code'],
                'product_order' => $product['product_order'],
                'product_tags' => array_values(Arr::get($organized['productTags'], $product['id'], [])),
                'product_properties' => array_values(Arr::get($organized['productProperties'], $product['id'], [])),
                'packages' => array_values(Arr::get($organized['packages'], $product['id'], [])),
                'galleries' => $this->getOrganizedGalleries($organized['galleries'], Product::class.'.'.$product['id']),
            ];
        }

        return ApiResponse::new()->data($output);
    }

    protected function getOrganizedGalleries(&$galleries, $key, $default = [])
    {
        $result = [];
        foreach (GalleryCategoryEnum::values() as $galleryCategoryEnumValue) {
            $result[$galleryCategoryEnumValue] = Arr::get($galleries, $galleryCategoryEnumValue.'.'.$key, $default);
        }

        return $result;
    }
}
