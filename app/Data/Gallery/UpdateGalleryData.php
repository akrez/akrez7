<?php

namespace App\Data\Gallery;

use App\Data\Data;
use Illuminate\Http\UploadedFile;

class UpdateGalleryData extends Data
{
    public function __construct(
        public ?UploadedFile $file,
        public $gallery_order,
        public $is_selected,
    ) {}

    public static function rules($context)
    {
        return [
            'gallery_order' => ['nullable', 'numeric'],
            'is_selected' => ['nullable', 'boolean'],
        ];
    }
}
