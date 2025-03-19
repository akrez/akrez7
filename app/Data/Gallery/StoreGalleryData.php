<?php

namespace App\Data\Gallery;

use Illuminate\Http\UploadedFile;

class StoreGalleryData extends GalleryData
{
    public function __construct(
        public ?UploadedFile $file,
        public $gallery_category,
        public $gallery_type,
        public $gallery_id,
        public $gallery_order,
        public $is_selected
    ) {}

    public static function rules($context)
    {
        return static::getRules($context, [
            'file' => true,
            'gallery_category' => true,
            'gallery_type' => true,
            'gallery_id' => true,
            'gallery_order' => false,
            'is_selected' => false,
        ]);
    }
}
