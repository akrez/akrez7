<?php

namespace App\Http\Controllers\Api;

use App\Data\TelegramMessage\StoreTelegramMessageData;
use App\Http\Controllers\Controller;
use App\Services\TelegramMessageService;

class TelegramMessageController extends Controller
{
    public function webhook(int $blog_id, string $telegram_token)
    {
        $storeTelegramMessageData = new StoreTelegramMessageData(
            $blog_id,
            $telegram_token,
            request()->getContent()
        );

        return TelegramMessageService::new()->webhook($storeTelegramMessageData);
    }
}
