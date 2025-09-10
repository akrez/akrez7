<?php

namespace App\Data\Gallery;

use Illuminate\Http\UploadedFile;

class StoreGalleryData extends GalleryData
{
    public function __construct(
        public int $blog_id,
        public ?UploadedFile $file,
        public $gallery_category,
        public $short_gallery_type,
        public $gallery_id,
        public $gallery_order,
        public $is_selected
    ) {}

    public function rules($context)
    {
        return $this->prepareRules($this->getRawRules($context), [
            'blog_id' => true,
            'file' => true,
            'gallery_category' => true,
            'short_gallery_type' => true,
            'gallery_id' => true,
            'gallery_order' => false,
            'is_selected' => false,
        ]);
    }
}
