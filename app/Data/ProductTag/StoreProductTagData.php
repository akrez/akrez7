<?php

namespace App\Data\ProductTag;

use App\Data\Data;
use App\Services\ProductTagService;
use App\Support\Arr;
use Illuminate\Validation\Validator;

class StoreProductTagData extends Data
{
    public function __construct(
        public int $blog_id,
        public int $product_id,
        public $tag_names,
        public array $safe_tag_names = []
    ) {}

    public function rules($context)
    {
        return [
            'blog_id' => ['required', 'integer'],
            'product_id' => ['required', 'integer'],
            'tag_names' => ['nullable'],
            'safe_tag_names.*' => ['max:'.ProductTagService::TAG_NAME_MAX_LENGTH],
        ];
    }

    public function attributes()
    {
        return [
            'safe_tag_names.*' => __('Tag'),
        ];
    }

    public function validate($context = null): Validator
    {
        $this->safe_tag_names = $this->filterTagNames($this->tag_names);

        $validator = parent::validate($context);

        $fromStar = 'safe_tag_names';
        $to = 'tag_names';

        foreach ($validator->errors()->get("{$fromStar}.*") as $key => $messages) {
            foreach ($messages as $message) {
                $validator->errors()->add($to, $message);
            }
            $validator->errors()->forget($key);
        }

        return $validator;
    }

    public function filterTagNames($tagNames)
    {
        $tagNames = (is_array($tagNames) ? $tagNames : [$tagNames]);

        $stringLine = collect($tagNames)->flatten()->implode(ProductTagService::NAME_GLUE);

        $tagNames = Arr::iexplode(array_keys(ProductTagService::NAME_SEPARATORS), $stringLine);

        return Arr::filterArray($tagNames);
    }
}
