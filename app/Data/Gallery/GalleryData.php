<?php

namespace App\Data\Gallery;

use App\Data\Data;
use App\Enums\GalleryCategoryEnum;
use App\Models\Blog;
use App\Models\Product;
use App\Services\GalleryService;
use Closure;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Validation\Rule;

abstract class GalleryData extends Data
{
    public function __construct() {}

    public function getRawRules($context)
    {
        return [
            'blog_id' => ['integer'],
            'id' => [],
            'gallery_category' => [Rule::enum(GalleryCategoryEnum::class)],
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
