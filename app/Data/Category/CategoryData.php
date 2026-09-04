<?php

namespace App\Data\Category;

use App\Data\Data;
use Illuminate\Validation\Rule;

class CategoryData extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $blog_id,
        public $name
    ) {}

    public function rules($context)
    {
        $uniqueRule = Rule::unique('categories')
            ->where('blog_id', $this->blog_id)
            ->where('name', $this->name);

        if ($this->id !== null) {
            $uniqueRule = $uniqueRule->ignore($this->id);
        }

        return [
            'blog_id' => ['required', 'integer'],
            'name' => ['required', 'max:64', $uniqueRule],
        ];
    }
}
