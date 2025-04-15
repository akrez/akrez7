<?php

namespace App\Services;

use App\Data\TelegramBot\StoreTelegramBotData;
use App\Data\TelegramBot\UpdateTelegramBotData;
use App\Data\TelegramBot\UploadTelegramBotData;
use App\Enums\TelegramBotStatusEnum;
use App\Http\Resources\TelegramBot\TelegramBotCollection;
use App\Http\Resources\TelegramBot\TelegramBotResource;
use App\Models\TelegramBot;
use App\Support\ApiResponse;
use App\Support\TelegramApi;
use App\Support\WebResponse;

class TelegramBotService extends Service
{
    public static function new()
    {
        return app(self::class);
    }

    public function getApiResource(int $blogId): ApiResponse
    {
        $model = $this->getLatestApiQuery($blogId)
            ->first();

        return ApiResponse::new(200)->data([
            'telegramBot' => (new TelegramBotResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'telegramBots' => (new TelegramBotCollection($models))->toArr(),
        ]);
    }

    protected function getLatestApiQuery($blogId)
    {
        return $this->getLatestBaseQuery($blogId)
            ->where('telegram_bot_status', TelegramBotStatusEnum::ACTIVE->value);
    }

    protected function getLatestBaseQuery($blogId)
    {
        return TelegramBot::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestTelegramBots(int $blogId)
    {
        $models = $this->getLatestBlogQuery($blogId)->get();

        return WebResponse::new()->data([
            'telegramBots' => (new TelegramBotCollection($models))->toArr(),
        ]);
    }

    public function storeTelegramBot(StoreTelegramBotData $storeTelegramBotData)
    {
        $webResponse = WebResponse::new()->input($storeTelegramBotData);

        $validation = $storeTelegramBotData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        if (! $this->setWebhook($storeTelegramBotData->telegram_token)) {
            return $webResponse->status(500);
        }

        $model = TelegramBot::create([
            'telegram_bot_status' => $storeTelegramBotData->telegram_bot_status,
            'telegram_token' => $storeTelegramBotData->telegram_token,
            'blog_id' => $storeTelegramBotData->blog_id,
        ]);
        if (! $model) {
            return $webResponse->status(500);
        }

        return $webResponse->status(201)->data($model)->message(__(':name is created successfully', [
            'name' => __('TelegramBot'),
        ]));
    }

    public function getTelegramBot(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $model = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $model) {
            return $webResponse->status(404);
        }

        return WebResponse::new()->data([
            'telegramBot' => (new TelegramBotResource($model))->toArr(),
        ]);
    }

    public function updateTelegramBot(UpdateTelegramBotData $updateTelegramBotData)
    {
        $webResponse = WebResponse::new()->input($updateTelegramBotData);

        $validation = $updateTelegramBotData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $model = $this->getLatestBlogQuery($updateTelegramBotData->blog_id)->where('id', $updateTelegramBotData->id)->first();
        if (! $model) {
            return $webResponse->status(404);
        }

        if ($model->telegram_token !== $updateTelegramBotData->telegram_token) {
            if (! $this->setWebhook($updateTelegramBotData->telegram_token)) {
                return $webResponse->status(500);
            }
        }

        $model->update([
            'telegram_bot_status' => $updateTelegramBotData->telegram_bot_status,
            'telegram_token' => $updateTelegramBotData->telegram_token,
        ]);
        if (! $model->save()) {
            return $webResponse->status(500);
        }

        return $webResponse
            ->status(201)
            ->data(['telegramBot' => (new TelegramBotResource($model))->toArr()])
            ->message(__(':name is updated successfully', [
                'name' => $model->name,
            ]));
    }

    public function destroyTelegramBot(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $telegramBot = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $telegramBot) {
            return $webResponse->status(404);
        }

        if (! $telegramBot->delete()) {
            return $webResponse->status(500);
        }

        return WebResponse::new(200)->message(__(':name is deleted successfully', [
            'name' => __('TelegramBot'),
        ]));
    }

    public function uploadTelegramBot(UploadTelegramBotData $uploadTelegramBotData)
    {
        $webResponse = WebResponse::new()->input($uploadTelegramBotData);

        $validation = $uploadTelegramBotData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $bot = $this->getLatestBlogQuery($uploadTelegramBotData->blog_id)->where('id', $uploadTelegramBotData->id)->first();
        if (! $bot) {
            return $webResponse->status(404);
        }

        $blog = BlogService::new()->getBlog($uploadTelegramBotData->blog_id)->getData('blog');

        $telegramApi = (new TelegramApi($bot['telegram_token']));

        if ($uploadTelegramBotData->attribute_name === 'name') {
            $response = $telegramApi->setMyName($blog['name']);
        } elseif ($uploadTelegramBotData->attribute_name === 'short_description') {
            $response = $telegramApi->setMyShortDescription($blog['short_description']);
        } elseif ($uploadTelegramBotData->attribute_name === 'description') {
            $response = $telegramApi->setMyDescription($blog['description']);
        } else {
            return WebResponse::new(400);
        }

        if (isset($response['ok']) and $response['ok']) {
            return WebResponse::new(200)->message(__(':name is updated successfully', [
                'name' => __('TelegramBot'),
            ]));
        }

        return WebResponse::new(500);
    }

    protected function setWebhook($telegramToken)
    {
        $url = TelegramMessageService::new()->getWebhookUrl($telegramToken);

        $telegramApi = new TelegramApi($telegramToken);
        $response = $telegramApi->setWebhook($url);

        return ! empty($response['ok']);
    }
}
