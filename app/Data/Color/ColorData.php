<?php

namespace App\Data\Color;

use App\Data\Data;
use Illuminate\Validation\Rule;

class ColorData extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $blog_id,
        public $code,
        public $name
    ) {}

    public function rules($context)
    {
        $uniqueRule = Rule::unique('colors')
            ->where('blog_id', $this->blog_id)
            ->where('code', $this->code);

        if ($this->id !== null) {
            $uniqueRule = $uniqueRule->ignore($this->id);
        }

        return [
            'blog_id' => ['required', 'integer'],
            'code' => ['required', 'max:16', 'regex:/^#[A-F0-9]{6}$/', $uniqueRule],
            'name' => ['required', 'max:31'],
        ];
    }
}
