<?php

namespace App\Data\CategoryProperty;

class UpdateCategoryPropertyData extends CategoryPropertyData
{
    public function rules($context)
    {
        return [
            'id' => ['required', 'integer'],
        ] + parent::rules($context);
    }
}
