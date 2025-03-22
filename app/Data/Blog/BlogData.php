<?php

namespace App\Data\Blog;

use App\Data\Data;
use App\Enums\BlogStatusEnum;
use Illuminate\Validation\Rule;

class BlogData extends Data
{
    public function __construct(
        public $name,
        public $short_description,
        public $description,
        public $blog_status
    ) {}

    public function rules($context)
    {
        return [
            'name' => 'required|max:64',
            'short_description' => 'required|max:120',
            'description' => 'required|max:512',
            'blog_status' => [Rule::in(BlogStatusEnum::values())],
        ];
    }
}
