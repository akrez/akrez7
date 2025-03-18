<?php

namespace App\Data\Gallery;

use App\Data\Data;
use Illuminate\Http\UploadedFile;

class StoreGalleryData extends Data
{
    public function __construct(
        public ?UploadedFile $file,
        public $gallery_order,
        public $is_selected,
    ) {}

    public static function rules($context)
    {
        return UpdateGalleryData::rules($context) + [
            'file' => [
                'required',
                'image',
                'mimes:jpeg,png,jpg,gif,svg',
                'max:2048',
            ],
        ];
    }
}
