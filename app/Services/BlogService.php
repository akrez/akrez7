<?php

namespace App\Services;

use App\Data\Blog\StoreBlogData;
use App\Data\Blog\UpdateBlogData;
use App\Enums\BlogStatusEnum;
use App\Enums\GalleryCategoryEnum;
use App\Http\Resources\Blog\BlogResource;
use App\Models\Blog;
use App\Models\Product;
use App\Support\ApiResponse;
use App\Support\Arr;
use App\Support\WebResponse;

class BlogService
{
    public static function new()
    {
        return app(self::class);
    }

    public function getApiResource(int $id)
    {
        $blog = Blog::query()
            ->where('id', $id)
            ->where('blog_status', BlogStatusEnum::ACTIVE->value)
            ->first();

        return ApiResponse::new($blog ? 200 : 404)->data([
            'blog' => (new BlogResource($blog))->toArr(),
        ]);
    }

    public function getBlog(int $id)
    {
        $blog = Blog::query()
            ->where('id', $id)
            ->first();

        return WebResponse::new($blog ? 200 : 404)->data([
            'blog' => (new BlogResource($blog))->toArr(),
        ]);
    }

    public function storeBlog(int $userId, StoreBlogData $blogData): WebResponse
    {
        $webResponse = WebResponse::new()->input($blogData);

        $validation = $blogData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $blog = Blog::create(['created_by' => $userId] + $validation->getData());
        if (! $blog) {
            return $webResponse->status(500);
        }

        return $webResponse->status(201)->data($blog)->message(__(':name is created successfully', [
            'name' => __('Blog'),
        ]));
    }

    public function updateBlog(Blog $blog, UpdateBlogData $blogData): WebResponse
    {
        $webResponse = WebResponse::new()->input($blogData);

        $validation = $blogData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $isSuccessful = $blog->update($validation->getData());
        if (! $isSuccessful) {
            return $webResponse->status(500);
        }

        return $webResponse->data($blog)->status(200)->message(__(':name is updated successfully', [
            'name' => __('Blog'),
        ]));
    }

    public function getUserBlog(int $userId, int $id)
    {
        $blog = Blog::query()
            ->where('id', $id)
            ->where('created_by', $userId)
            ->first();

        return WebResponse::new($blog ? 200 : 404)->data([
            'blog' => $blog,
        ]);
    }

    public function getLatestUserBlogs(int $userId)
    {
        $blogs = Blog::query()
            ->where('created_by', $userId)
            ->latest('created_at')
            ->get();

        return WebResponse::new()->data([
            'blogs' => $blogs,
        ]);
    }

    public function getActiveBlog(int $id)
    {
        $blog = Blog::query()
            ->where('id', $id)
            ->where('blog_status', BlogStatusEnum::ACTIVE->value)
            ->first();

        return WebResponse::new($blog ? 200 : 404)->data([
            'blog' => $blog,
        ]);
    }

    public function getApiResponse(int $id)
    {
        $blogResponse = $this->getBlog($id);
        if (! $blogResponse->isSuccessful()) {
            return ApiResponse::new($blogResponse->getStatus());
        }

        $raw = [
            'blog' => $blogResponse->getData('blog'),
            'colors' => ColorService::new()->getApiCollection($id)->getData('colors'),
            'contacts' => ContactService::new()->getApiCollection($id)->getData('contacts'),
            'packages' => PackageService::new()->getApiCollection($id)->getData('packages'),
            'products' => ProductService::new()->getApiCollection($id)->getData('products'),
            'galleries' => GalleryService::new()->getApiCollection($id)->getData('galleries'),
            'productTags' => ProductTagService::new()->getApiCollection($id)->getData('product_tags'),
            'productProperties' => ProductPropertyService::new()->getApiCollection($id)->getData('product_properties'),
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
