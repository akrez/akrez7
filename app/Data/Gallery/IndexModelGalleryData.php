<?php

namespace App\Data\Gallery;

class IndexModelGalleryData extends GalleryData
{
    public function __construct(
        public int $blog_id,
        public $gallery_category,
        public $gallery_type,
        public $gallery_id
    ) {}

    public function rules($context)
    {
        return $this->prepareRules($this->getRawRules($context), [
            'blog_id' => true,
            'gallery_category' => true,
            'gallery_type' => true,
            'gallery_id' => true,
        ]);
    }
}
