<?php

namespace App\Data\Gallery;

use Intervention\Image\Encoders\AutoEncoder;

class EffectGalleryData extends GalleryData
{
    public function __construct(
        public $gallery_category,
        public $whmq,
        public $name,
        protected $width = null,
        protected $height = null,
        protected $mode = null,
        protected $quality = null
    ) {
        [
            $this->width,
            $this->height,
            $this->mode,
            $this->quality,
        ] = explode('_', $whmq) + array_fill(0, 4, null);
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getMode()
    {
        return $this->mode;
    }

    public function getQuality()
    {
        return $this->quality ?? AutoEncoder::DEFAULT_QUALITY;
    }

    public function rules($context)
    {
        return $this->prepareRules($this->getRules($context), [
            'gallery_category' => true,
            'whmq' => true,
            'name' => true,
            //
            'width' => false,
            'height' => false,
            'mode' => false,
            'quality' => false,
        ]);
    }
}
