<?php

namespace App\Data\Package;

use App\Data\Data;
use App\Enums\PackageStatusEnum;
use App\Services\ColorService;
use Illuminate\Validation\Rule;

abstract class PackageData extends Data
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

    public function getRules($context)
    {
        return [
            'id' => ['integer'],
            'product_id' => ['integer'],
            'blog_id' => ['integer'],
            'price' => ['numeric'],
            'guaranty' => ['string', 'max:256'],
            'description' => ['string', 'max:2048'],
            'package_status' => [Rule::in(PackageStatusEnum::values())],
            'color_id' => [
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
