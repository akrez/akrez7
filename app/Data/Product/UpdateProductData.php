<?php

namespace App\Data\Product;

class UpdateProductData extends ProductData
{
    public function rules($context)
    {
        return [
            'id' => ['required', 'integer'],
        ] + parent::rules($context);
    }
}
