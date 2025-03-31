<?php

namespace App\Data\Package;

class UpdatePackageData extends PackageData
{
    public function rules($context)
    {
        return [
            'id' => ['required', 'integer'],
        ] + parent::rules($context);
    }
}
