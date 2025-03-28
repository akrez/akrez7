<?php

namespace App\Data\ProductProperty;

use App\Data\Data;
use App\Services\ProductPropertyService;
use App\Support\Arr;
use Illuminate\Validation\Validator;

class StoreProductPropertyData extends Data
{
    public function __construct(
        public int $blog_id,
        public int $product_id,
        public $keys_values,
        public array $safe_keys_values = []
    ) {}

    public function rules($context)
    {
        return [
            'blog_id' => ['required', 'integer'],
            'product_id' => ['required', 'integer'],
            'keys_values' => ['nullable'],
            'safe_keys_values' => ['array'],
            'safe_keys_values.*.property_key' => ['max:'.ProductPropertyService::PROPERTY_MAX_LENGTH],
            'safe_keys_values.*.property_values' => ['array'],
            'safe_keys_values.*.property_values.*' => ['max:'.ProductPropertyService::PROPERTY_MAX_LENGTH],
        ];
    }

    public function attributes()
    {
        return [
            'keys_values' => __('Property'),
            'safe_keys_values' => __('Property'),
            'safe_keys_values.*.property_key' => __('Property'),
            'safe_keys_values.*.property_values' => __('Property'),
            'safe_keys_values.*.property_values.*' => __('Property'),
        ];
    }

    public function validate($context = null): Validator
    {
        $this->safe_keys_values = $this->filterKeysValues($this->keys_values);

        $validator = parent::validate($context);

        $fromStar = 'safe_keys_values';
        $to = 'keys_values';

        foreach ($validator->errors()->get("{$fromStar}.*") as $key => $messages) {
            foreach ($messages as $message) {
                $validator->errors()->add($to, $message);
            }
            $validator->errors()->forget($key);
        }

        return $validator;
    }

    public function filterKeysValues($content)
    {
        $lines = Arr::iexplode(array_keys(ProductPropertyService::LINES_SEPARATORS), $content);
        //
        $keyValuesArray = [];
        foreach ($lines as $line) {
            $keyValuesArray[] = Arr::iexplode(array_keys(ProductPropertyService::KEY_VALUES_SEPARATORS), $line);
        }
        //
        $keyToValues = [];
        foreach ($keyValuesArray as $keyValues) {
            $keyValues += array_fill(0, 2, '');
            //
            $key = trim($keyValues[0]);
            //
            if (! array_key_exists($key, $keyToValues)) {
                $keyToValues[$key] = [];
            }
            //
            $keyToValues[$key] = array_merge($keyToValues[$key], array_slice($keyValues, 1));
        }
        //
        $result = [];
        foreach ($keyToValues as $key => $values) {
            $result[] = [
                'property_key' => $key,
                'property_values' => Arr::filterArray($keyToValues[$key]),
            ];
        }

        return $result;
    }
}
