<?php

namespace App\Services;

use App\Enums\GalleryCategoryEnum;
use App\Models\Blog;
use App\Models\Product;
use App\Support\ApiResponse;
use App\Support\Arr;
use App\Support\Cache;
use DateTime;
use SimpleXMLElement;

class SummaryService
{
    public static function new()
    {
        return app(self::class);
    }

    public function forgetCachedApiResponse(int $blogId)
    {
        Cache::forget($this->showSummaryCacheKey($blogId));
    }

    public function getCachedApiResponse(int $blogId, \Illuminate\Http\Request $request, bool $forgetCache = false): ApiResponse
    {
        if ($forgetCache) {
            $this->forgetCachedApiResponse($blogId);
        }

        $response = Cache::remember($this->showSummaryCacheKey($blogId), 3600, function () use ($blogId) {
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

        $domain = $request->route()->parameter('domain');
        if ($domain) {
            $loc = route('domains.show', ['domain' => $domain]);
        } else {
            $loc = route('summaries.show', ['blog_id' => $blogId]);
        }

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

    protected function showSummaryCacheKey(int $blogId)
    {
        return Cache::keyShowSummary($blogId);
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
