<?php

namespace App\Data\Blog;

use App\Data\Data;

class UpdateBlogData extends Data
{
    public function __construct(
        public $name,
        public $short_description,
        public $description,
        public $blog_status
    ) {}

    public static function rules($context)
    {
        return StoreBlogData::rules($context);
    }
}
