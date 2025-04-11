<?php

namespace App\Services;

use App\Data\TelegramBot\StoreTelegramBotData;
use App\Data\TelegramBot\UpdateTelegramBotData;
use App\Http\Resources\TelegramBot\TelegramBotCollection;
use App\Http\Resources\TelegramBot\TelegramBotResource;
use App\Models\TelegramBot;
use App\Support\ApiResponse;
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

        $model = TelegramBot::create([
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

        $model->update([
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
}
