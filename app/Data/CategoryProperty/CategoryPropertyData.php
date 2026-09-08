<?php

namespace App\Data\CategoryProperty;

use App\Data\Data;
use App\Enums\CategoryPropertyTypeEnum;
use Illuminate\Validation\Rule;

class CategoryPropertyData extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $blog_id,
        public $category_id,
        public $property_key,
        public $filter_type,
        public $unit
    ) {}

    public function rules($context)
    {
        return [
            'blog_id' => ['required', 'integer'],
            'category_id' => ['required', 'integer'],
            'property_key' => ['required', 'max:64'],
            'filter_type' => ['nullable', Rule::in(implode(',', CategoryPropertyTypeEnum::values()))],
            'unit' => ['nullable', 'max:31'],
        ];
    }

    public function attributes()
    {
        return [
            'property_key' => __('validation.attributes.property_key'),
            'category_id' => __('validation.attributes.category_id'),
            'filter_type' => __('validation.attributes.filter_type'),
            'unit' => __('validation.attributes.unit'),
        ];
    }
}
