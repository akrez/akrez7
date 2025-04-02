<?php

namespace App\Data\Package;

class UpdatePackageData extends PackageData
{
    public function __construct(
        public ?int $id,
        public int $product_id,
        public int $blog_id,
        public $package_status
    ) {}

    public function rules($context)
    {
        return $this->getRules($context, [
            'id' => true,
            'package_status' => true,
        ]);
    }
}
