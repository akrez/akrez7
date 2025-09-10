<?php

namespace App\Data\Gallery;

use App\Data\Data;
use App\Enums\GalleryCategoryEnum;
use App\Models\Blog;
use App\Models\Product;
use App\Services\GalleryService;
use Closure;
use Illuminate\Validation\Rule;

abstract class GalleryData extends Data
{
    public $short_gallery_type;

    public function __construct() {}

    public function toGalleryType()
    {
        return 'App\\Models\\'.$this->short_gallery_type;
    }

    public function getRules($context)
    {
        return [
            'blog_id' => ['integer'],
            'id' => [],
            'gallery_category' => [Rule::enum(GalleryCategoryEnum::class)],
            'short_gallery_type' => [Rule::in([Blog::getClassName(), Product::getClassName()])],
            'gallery_id' => ['integer'],
            'gallery_order' => ['numeric'],
            'is_selected' => ['boolean'],
            'file' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
            //
            'width' => ['nullable', 'integer', 'min:1', 'max:'.GalleryService::MAX_SIZE],
            'height' => ['nullable', 'integer', 'min:1', 'max:'.GalleryService::MAX_SIZE],
            'mode' => ['nullable', Rule::in(GalleryService::VALID_MODES)],
            'quality' => ['nullable', 'integer', 'min:1', 'max:100'],
            //
            'name' => [],
            'whmq' => [
                'regex:'.GalleryService::WHMQ_REGEX_PATTERN,
                function (string $attribute, mixed $value, Closure $fail) {
                    if (count(explode($value, '_')) >= 5) {
                        $fail(__('validation.regex', [
                            'attribute' => __('validation.attributes.'.$attribute),
                        ]));
                    }
                },
            ],
        ];
    }
}
