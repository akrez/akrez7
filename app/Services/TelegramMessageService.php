<?php

namespace App\Services;

use App\Data\TelegramMessage\StoreTelegramMessageData;
use App\Enums\TelegramMessageProcessStatusEnum;
use App\Models\TelegramMessage;
use App\Support\ApiResponse;
use App\Support\Arr;

class TelegramMessageService
{
    const CATEGORY_PREFIX = '🗂 | ';

    const CONTACT_US = '☎️ | ارتباط با ما';

    public static function new()
    {
        return app(self::class);
    }

    public function getWebhookUrl($blogId, $telegramToken)
    {
        return route('telegram_messages.webhook', [
            'blog_id' => $blogId,
            'telegram_token' => $telegramToken,
        ]);
    }

    public function webhook(StoreTelegramMessageData $storeTelegramMessageData)
    {
        $storeResponse = $this->store($storeTelegramMessageData);
        if (! $storeResponse->isSuccessful()) {
            return $storeResponse->input($storeTelegramMessageData);
        }

        $processResponse = $this->process($storeResponse->getData('telegramMessage'));
        if (! $processResponse->isSuccessful()) {
            return $processResponse->input($storeTelegramMessageData);
        }
    }

    protected function store(StoreTelegramMessageData $storeTelegramMessageData)
    {
        $validation = $storeTelegramMessageData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return ApiResponse::new(422)->errors($validation->errors());
        }

        $contentJson = (array) json_decode($storeTelegramMessageData->content_json, true);

        $telegramMessage = TelegramMessage::create([
            'blog_id' => $storeTelegramMessageData->blog_id,
            'telegram_token' => $storeTelegramMessageData->telegram_token,
            'content_json' => $contentJson,
        ]);

        if (! $telegramMessage) {
            return ApiResponse::new(500);
        }

        return ApiResponse::new(201)->data([
            'telegramMessage' => $telegramMessage,
        ]);
    }

    protected function process(TelegramMessage $telegramMessage)
    {
        $telegramMessage->update(['process_status' => TelegramMessageProcessStatusEnum::PROCESSING]);

        $updateId = Arr::get($telegramMessage->content_json, 'update_id');
        $chatId = Arr::get($telegramMessage->content_json, 'message.chat.id');
        $messageText = Arr::get($telegramMessage->content_json, 'message.text');
        if (empty($updateId) || empty($chatId) || empty($messageText)) {
            $telegramMessage->update(['process_status' => TelegramMessageProcessStatusEnum::NOT_VALID]);

            return ApiResponse::new(403);
        }

        $telegramBotResponse = TelegramBotService::new()->getApiResourceByTelegramToken(
            $telegramMessage->blog_id,
            $telegramMessage->telegram_token
        );
        if (! $telegramBotResponse->isSuccessful()) {
            $telegramMessage->update(['process_status' => TelegramMessageProcessStatusEnum::NOT_VALID]);

            return ApiResponse::new(403);
        }
        $telegramBotResource = $telegramBotResponse->getData('telegramBot');

        $wasUpdated = $telegramMessage->update([
            'blog_id' => $telegramBotResource['blog_id'],
            'bot_id' => $telegramBotResource['id'],
            'update_id' => $updateId,
            'chat_id' => $chatId,
            'message_text' => $messageText,
        ]);

        if (! $wasUpdated) {
            $telegramMessage->update(['process_status' => TelegramMessageProcessStatusEnum::NOT_VALID]);

            return ApiResponse::new(500);
        }

        $telegramMessage->update(['process_status' => TelegramMessageProcessStatusEnum::VALID]);

        return ApiResponse::new(200)->data([
            'telegramMessage' => ($telegramMessage),
        ]);
    }
}
