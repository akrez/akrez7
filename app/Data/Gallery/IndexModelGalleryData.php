<?php

namespace App\Data\Gallery;

class IndexModelGalleryData extends GalleryData
{
    public function __construct(
        public int $blog_id,
        public $gallery_category,
        public $short_gallery_type,
        public $gallery_id
    ) {}

    public function rules($context)
    {
        return static::getRules($context, [
            'blog_id' => true,
            'gallery_category' => true,
            'short_gallery_type' => true,
            'gallery_id' => true,
        ]);
    }
}
