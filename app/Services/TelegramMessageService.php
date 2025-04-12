<?php

namespace App\Services;

class TelegramMessageService
{
    public static function new()
    {
        return app(self::class);
    }

    public function getWebhookUrl($telegramToken)
    {
        return route('telegram_messages.webhook', [
            'telegram_token' => $telegramToken,
        ]);
    }
}
