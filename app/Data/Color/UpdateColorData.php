<?php

namespace App\Data\Color;

class UpdateColorData extends ColorData
{
    public function rules($context)
    {
        return [
            'id' => ['required', 'integer'],
        ] + parent::rules($context);
    }
}
