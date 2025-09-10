<?php

namespace App\Data\Gallery;

class IndexCategoryGalleryData extends GalleryData
{
    public function __construct(
        public int $blog_id,
        public $gallery_category
    ) {}

    public function rules($context)
    {
        return $this->prepareRules($this->getRawRules($context), [
            'blog_id' => true,
            'gallery_category' => true,
        ]);
    }
}
