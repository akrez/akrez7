<?php

namespace App\Services;

use App\Data\Blog\StoreBlogData;
use App\Data\Blog\UpdateBlogData;
use App\Enums\BlogStatus;
use App\Models\Blog;
use App\Support\WebResponse;

class BlogService
{
    public static function new()
    {
        return app(self::class);
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

        $validation = $blogData->validate(false);
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

    public function getUserBlogs(int $userId)
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
            ->where('blog_status', BlogStatus::ACTIVE->value)
            ->first();

        return WebResponse::new($blog ? 200 : 404)->data([
            'blog' => $blog,
        ]);
    }
}
