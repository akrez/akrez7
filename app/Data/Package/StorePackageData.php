<?php

namespace App\Data\Package;

class StorePackageData extends PackageData
{
    public function rules($context)
    {
        return $this->getRules($context, [
            'product_id' => true,
            'blog_id' => true,
            'price' => true,
            'package_status' => true,
            'color_id' => false,
            'guaranty' => false,
            'description' => false,
        ]);
    }
}
