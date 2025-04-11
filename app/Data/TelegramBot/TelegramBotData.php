<?php

namespace App\Data\TelegramBot;

use App\Data\Data;
use Illuminate\Validation\Rule;

class TelegramBotData extends Data
{
    public function __construct(
        public ?int $id,
        public ?int $blog_id,
        public $telegram_token
    ) {}

    public function rules($context)
    {
        $uniqueRule = Rule::unique('telegram_bots', 'telegram_token')
            ->where('blog_id', $this->blog_id);

        if ($this->id !== null) {
            $uniqueRule = $uniqueRule->ignore($this->id);
        }

        return [
            'telegram_token' => ['required', 'max:64', 'regex:/^\d{10,}:[A-Za-z0-9_-]{35}$/', $uniqueRule],
        ];
    }
}
