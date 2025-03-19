<?php

namespace App\Data\Gallery;

use App\Data\Data;
use App\Enums\GalleryCategoryEnum;
use App\Models\Blog;
use Illuminate\Validation\Rule;

abstract class GalleryData extends Data
{
    public $gallery_type;

    public function __construct() {}

    public function galleryType()
    {
        return 'App\\Models\\' . $this->gallery_type;
    }

    public static function getRules($context, $attributesToRequired)
    {
        $rules = [
            'id' => [],
            'gallery_category' => [Rule::enum(GalleryCategoryEnum::class)],
            'gallery_type' => [Rule::in([Blog::getClassName()])],
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
