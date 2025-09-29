<?php

namespace App\Data\Package;

class UpdatePackageData extends PackageData
{
    public function __construct(
        public ?int $id,
        public int $product_id,
        public int $blog_id,
        public $package_status,
        public $price,
        public $show_price
    ) {}

    public function rules($context)
    {
        return $this->prepareRules($this->getRawRules($context), [
            'id' => true,
            'product_id' => true,
            'blog_id' => true,
            'price' => true,
            'package_status' => true,
            'show_price' => false,
        ]);
    }
}
