<?php

namespace App\Services;

use App\Data\Blog\StoreBlogData;
use App\Data\Blog\UpdateBlogData;
use App\Enums\BlogStatusEnum;
use App\Models\Blog;
use App\Support\ResponseBuilder;

class BlogService
{
    public static function new()
    {
        return app(self::class);
    }

    public function storeBlog(int $userId, StoreBlogData $blogData): ResponseBuilder
    {
        $responseBuilder = ResponseBuilder::new()->input($blogData);

        $validation = $blogData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $blog = Blog::create(['created_by' => $userId] + $validation->getData());
        if (! $blog) {
            return $responseBuilder->status(500);
        }

        return $responseBuilder->status(201)->data($blog)->message(__(':name is created successfully', [
            'name' => __('Blog'),
        ]));
    }

    public function updateBlog(Blog $blog, UpdateBlogData $blogData): ResponseBuilder
    {
        $responseBuilder = ResponseBuilder::new()->input($blogData);

        $validation = $blogData->validate(false);
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $isSuccessful = $blog->update($validation->getData());
        if (! $isSuccessful) {
            return $responseBuilder->status(500);
        }

        return $responseBuilder->data($blog)->status(200)->message(__(':name is updated successfully', [
            'name' => __('Blog'),
        ]));
    }

    public function getUserBlog(int $userId, int $id)
    {
        $blog = Blog::query()
            ->where('id', $id)
            ->where('created_by', $userId)
            ->first();

        return ResponseBuilder::new($blog ? 200 : 404)->data([
            'blog' => $blog,
        ]);
    }

    public function getUserBlogs(int $userId)
    {
        $blogs = Blog::query()
            ->where('created_by', $userId)
            ->latest('created_at')
            ->get();

        return ResponseBuilder::new()->data([
            'blogs' => $blogs,
        ]);
    }

    public function getActiveBlog(int $id)
    {
        $blog = Blog::query()
            ->where('id', $id)
            ->where('blog_status', BlogStatusEnum::ACTIVE->value)
            ->first();

        return ResponseBuilder::new($blog ? 200 : 404)->data([
            'blog' => $blog,
        ]);
    }
}
