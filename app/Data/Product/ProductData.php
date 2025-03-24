<?php

namespace App\Data\Product;

use App\Data\Data;
use App\Enums\ProductStatusEnum;
use App\Rules\SimpleWordRule;
use Illuminate\Validation\Rule;

class ProductData extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $blog_id,
        public $code,
        public $name,
        public $product_status,
        public $product_order
    ) {}

    public function rules($context)
    {
        $uniqueRule = Rule::unique('products')
            ->where('blog_id', $this->blog_id)
            ->where('code', $this->code);

        if ($this->id !== null) {
            $uniqueRule = $uniqueRule->ignore($this->id);
        }

        return [
            'name' => ['required', 'max:64'],
            'code' => ['required', 'max:32', new SimpleWordRule, $uniqueRule],
            'product_status' => [Rule::in(ProductStatusEnum::values())],
            'product_order' => ['nullable', 'numeric'],
        ];
    }
}
