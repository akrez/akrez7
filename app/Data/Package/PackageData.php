<?php

namespace App\Data\Package;

use App\Data\Data;
use App\Enums\PackageStatusEnum;
use App\Services\ColorService;
use Illuminate\Validation\Rule;

class PackageData extends Data
{
    public function __construct(
        public ?int $id,
        public int $product_id,
        public int $blog_id,
        public $price,
        public $package_status,
        public $color_id,
        public $guaranty,
        public $description
    ) {}

    public function rules($context)
    {
        return [
            'product_id' => ['required', 'integer'],
            'blog_id' => ['required', 'integer'],
            'price' => ['required', 'numeric'],
            'guaranty' => ['nullable', 'string', 'max:256'],
            'description' => ['nullable', 'string', 'max:2048'],
            'package_status' => ['required', Rule::in(PackageStatusEnum::values())],
            'color_id' => [
                'nullable',
                function ($attribute, $value, $fail) {
                    if ($value !== null) {
                        $isValidColor = ColorService::new()->getColor($this->blog_id, $this->color_id)->isSuccessful();
                        if (! $isValidColor) {
                            $fail(__('validation.exists', [
                                'Attribute' => __('validation.attributes.color_id'),
                            ]));
                        }
                    }
                },
            ],
        ];
    }
}
