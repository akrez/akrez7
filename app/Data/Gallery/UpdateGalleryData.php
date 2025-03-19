<?php

namespace App\Data\Gallery;

class UpdateGalleryData extends GalleryData
{
    public function __construct(
        public $gallery_order,
        public $is_selected,
    ) {}

    public static function rules($context)
    {
        return static::getRules($context, [
            'gallery_order' => false,
            'is_selected' => false,
        ]);
    }
}
