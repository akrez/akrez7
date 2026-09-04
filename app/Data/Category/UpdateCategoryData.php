<?php

namespace App\Data\Category;

class UpdateCategoryData extends CategoryData
{
    public function rules($context)
    {
        return [
            'id' => ['required', 'integer'],
        ] + parent::rules($context);
    }
}
