<?php

namespace App\Data\Gallery;

use App\Data\Data;
use App\Enums\GalleryCategoryEnum;
use App\Models\Blog;
use App\Models\Product;
use Illuminate\Validation\Rule;

abstract class GalleryData extends Data
{
    public $short_gallery_type;

    public function __construct() {}

    public function toGalleryType()
    {
        return 'App\\Models\\'.$this->short_gallery_type;
    }

    public static function getRules($context, $attributesToRequired)
    {
        $rules = [
            'blog_id' => ['integer'],
            'id' => [],
            'gallery_category' => [Rule::enum(GalleryCategoryEnum::class)],
            'short_gallery_type' => [Rule::in([Blog::getClassName(), Product::getClassName()])],
            'gallery_id' => ['integer'],
            'gallery_order' => ['numeric'],
            'is_selected' => ['boolean'],
            'file' => ['image', 'mimes:jpeg,png,jpg,gif,svg', 'max:2048'],
        ];

        $result = [];
        foreach ($attributesToRequired as $attribute => $required) {
            $result[$attribute] = array_merge(
                [$required ? 'required' : 'nullable'],
                $rules[$attribute]
            );
        }

        return $result;
    }
}
