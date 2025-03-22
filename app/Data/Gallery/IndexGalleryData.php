<?php

namespace App\Data\Gallery;

class IndexGalleryData extends GalleryData
{
    public function __construct(
        public $gallery_category,
        public $short_gallery_type,
        public $gallery_id
    ) {}

    public static function rules($context)
    {
        return static::getRules($context, [
            'gallery_category' => true,
            'short_gallery_type' => true,
            'gallery_id' => true,
        ]);
    }
}
