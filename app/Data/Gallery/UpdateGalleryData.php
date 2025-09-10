<?php

namespace App\Data\Gallery;

class UpdateGalleryData extends GalleryData
{
    public function __construct(
        public int $id,
        public int $blog_id,
        public $gallery_order,
        public $is_selected,
    ) {}

    public function rules($context)
    {
        return $this->prepareRules($this->getRules($context), [
            'blog_id' => true,
            'id' => true,
            'gallery_order' => false,
            'is_selected' => false,
        ]);
    }
}
