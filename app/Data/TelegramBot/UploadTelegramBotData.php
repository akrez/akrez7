<?php

namespace App\Data\TelegramBot;

use App\Data\Data;
use Illuminate\Validation\Rule;

class UploadTelegramBotData extends Data
{
    public function __construct(
        public $id,
        public $blog_id,
        public $attribute_name
    ) {}

    public function rules($context)
    {
        return [
            'id' => ['required', 'integer'],
            'blog_id' => ['required', 'integer'],
            'attribute_name' => ['required', Rule::in(['name', 'short_description', 'description'])],
        ];
    }
}
