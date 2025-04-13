<?php

namespace App\Data\TelegramBot;

use App\Data\Data;
use App\Rules\TelegramTokenRule;
use Illuminate\Validation\Rule;

class TelegramBotData extends Data
{
    public function __construct(
        public $id,
        public $blog_id,
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
            'blog_id' => ['required', 'integer'],
            'telegram_token' => ['required', 'max:64', new TelegramTokenRule, $uniqueRule],
        ];
    }
}
