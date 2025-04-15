<?php

namespace App\Data\TelegramMessage;

use App\Data\Data;
use App\Rules\TelegramTokenRule;

class StoreTelegramMessageData extends Data
{
    public function __construct(
        public $blog_id,
        public $telegram_token,
        public $content_json
    ) {}

    public function rules($context)
    {
        return [
            'blog_id' => ['required', 'integer'],
            'telegram_token' => ['required', 'max:64', new TelegramTokenRule],
            'content_json' => ['required'],
        ];
    }
}
