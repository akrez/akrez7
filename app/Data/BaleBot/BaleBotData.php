<?php

namespace App\Data\BaleBot;

use App\Data\Data;
use App\Enums\BaleBotStatusEnum;
use App\Rules\BaleTokenRule;
use Illuminate\Validation\Rule;

class BaleBotData extends Data
{
    public function __construct(
        public $id,
        public $blog_id,
        public $bale_token,
        public $bale_bot_status,
        public $user_service,
        public $notify_admin_on_invoice,
        public $admin
    ) {}

    public function rules($context)
    {
        $uniqueRule = Rule::unique('bale_bots', 'bale_token')
            ->where('blog_id', $this->blog_id);

        if ($this->id !== null) {
            $uniqueRule = $uniqueRule->ignore($this->id);
        }

        return [
            'blog_id' => ['required', 'integer'],
            'bale_token' => ['required', 'max:64', new BaleTokenRule, $uniqueRule],
            'bale_bot_status' => ['required', Rule::enum(BaleBotStatusEnum::class)],
            'user_service' => ['nullable', 'boolean'],
            'notify_admin_on_invoice' => ['nullable', 'boolean'],
            'admin' => ['nullable', 'string', 'max:128'],
        ];
    }
}
