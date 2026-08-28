<?php

namespace App\Services;

use App\Data\BaleBot\StoreBaleBotData;
use App\Data\BaleBot\UpdateBaleBotData;
use App\Data\BaleBot\UploadBaleBotData;
use App\Enums\BaleBotStatusEnum;
use App\Http\Resources\BaleBot\BaleBotCollection;
use App\Http\Resources\BaleBot\BaleBotResource;
use App\Models\BaleBot;
use App\Support\ApiResponse;
use App\Support\BaleApi;
use App\Support\WebResponse;

class BaleBotService extends Service
{
    public static function new()
    {
        return app(self::class);
    }

    public function getApiResource(int $blogId, int $id): ApiResponse
    {
        $model = $this->getLatestApiQuery($blogId)
            ->where('id', $id)
            ->first();

        return ApiResponse::new(200)->data([
            'baleBot' => (new BaleBotResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'baleBots' => (new BaleBotCollection($models))->toArr(),
        ]);
    }

    protected function getLatestApiQuery($blogId)
    {
        return $this->getLatestBaseQuery($blogId)
            ->where('bale_bot_status', BaleBotStatusEnum::ACTIVE->value);
    }

    protected function getLatestBaseQuery($blogId): \Illuminate\Database\Eloquent\Builder
    {
        return BaleBot::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestBaleBots(int $blogId)
    {
        $models = $this->getLatestBlogQuery($blogId)->get();

        return WebResponse::new()->data([
            'baleBots' => (new BaleBotCollection($models))->toArr(),
        ]);
    }

    public function getApiResourceByBaleToken(int $blogId, string $baleToken): ApiResponse
    {
        $model = $this->getLatestApiQuery($blogId)
            ->where('bale_token', $baleToken)
            ->first();

        return ApiResponse::new(200)->data([
            'baleBot' => $model ? (new BaleBotResource($model))->toArr() : null,
        ]);
    }

    public function storeBaleBot(StoreBaleBotData $storeBaleBotData)
    {
        $webResponse = WebResponse::new()->input($storeBaleBotData);

        $validation = $storeBaleBotData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        if (! $this->setWebhook($storeBaleBotData->blog_id, $storeBaleBotData->bale_token)) {
            return $webResponse->status(500);
        }

        $model = BaleBot::create([
            'bale_bot_status' => $storeBaleBotData->bale_bot_status,
            'bale_token' => $storeBaleBotData->bale_token,
            'user_service' => $storeBaleBotData->user_service,
            'notify_admin_on_invoice' => $storeBaleBotData->notify_admin_on_invoice,
            'admin' => $storeBaleBotData->admin,
            'blog_id' => $storeBaleBotData->blog_id,
        ]);
        if (! $model) {
            return $webResponse->status(500);
        }

        return $webResponse->status(201)->data($model)->message(__(':name is created successfully', [
            'name' => __('BaleBot'),
        ]));
    }

    public function getBaleBot(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $model = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $model) {
            return $webResponse->status(404);
        }

        return WebResponse::new()->data([
            'baleBot' => (new BaleBotResource($model))->toArr(),
        ]);
    }

    public function updateBaleBot(UpdateBaleBotData $updateBaleBotData)
    {
        $webResponse = WebResponse::new()->input($updateBaleBotData);

        $validation = $updateBaleBotData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $model = $this->getLatestBlogQuery($updateBaleBotData->blog_id)->where('id', $updateBaleBotData->id)->first();
        if (! $model) {
            return $webResponse->status(404);
        }

        if ($model->bale_token !== $updateBaleBotData->bale_token) {
            if (! $this->setWebhook($updateBaleBotData->blog_id, $updateBaleBotData->bale_token)) {
                return $webResponse->status(500);
            }
        }

        $model->update([
            'bale_bot_status' => $updateBaleBotData->bale_bot_status,
            'bale_token' => $updateBaleBotData->bale_token,
            'user_service' => $updateBaleBotData->user_service,
            'notify_admin_on_invoice' => $updateBaleBotData->notify_admin_on_invoice,
            'admin' => $updateBaleBotData->admin,
        ]);
        if (! $model->save()) {
            return $webResponse->status(500);
        }

        return $webResponse
            ->status(201)
            ->data(['baleBot' => (new BaleBotResource($model))->toArr()])
            ->message(__(':name is updated successfully', [
                'name' => $model->name,
            ]));
    }

    public function destroyBaleBot(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $baleBot = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $baleBot) {
            return $webResponse->status(404);
        }

        if (! $baleBot->delete()) {
            return $webResponse->status(500);
        }

        return WebResponse::new(200)->message(__(':name is deleted successfully', [
            'name' => __('BaleBot'),
        ]));
    }

    public function uploadBaleBot(UploadBaleBotData $uploadBaleBotData)
    {
        $webResponse = WebResponse::new()->input($uploadBaleBotData);

        $validation = $uploadBaleBotData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $bot = $this->getLatestBlogQuery($uploadBaleBotData->blog_id)->where('id', $uploadBaleBotData->id)->first();
        if (! $bot) {
            return $webResponse->status(404);
        }

        $blog = BlogService::new()->getBlog($uploadBaleBotData->blog_id)->getData('blog');

        $baleApi = (new BaleApi($bot['bale_token']));

        if ($uploadBaleBotData->attribute_name === 'name') {
            $response = $baleApi->setMyName($blog['name']);
        } elseif ($uploadBaleBotData->attribute_name === 'short_description') {
            $response = $baleApi->setMyShortDescription($blog['short_description']);
        } elseif ($uploadBaleBotData->attribute_name === 'description') {
            $response = $baleApi->setMyDescription($blog['description']);
        } else {
            return WebResponse::new(400);
        }

        if (isset($response['ok']) and $response['ok']) {
            return WebResponse::new(200)->message(__(':name is updated successfully', [
                'name' => __('BaleBot'),
            ]));
        }

        return WebResponse::new(500);
    }

    public function notifyAdminForInvoice(int $blogId, array $invoiceData)
    {
        $baleBots = $this->getLatestBaseQuery($blogId)
            ->where('bale_bot_status', BaleBotStatusEnum::ACTIVE->value)
            ->where('notify_admin_on_invoice', true)
            ->whereNotNull('admin')
            ->where('admin', '<>', '')
            ->get();

        $subject = [];
        foreach ($invoiceData as $key => $value) {
            $subject[] = '**'.$key.':**'.' '.$value;
        }
        $text = implode("\n", $subject);

        foreach ($baleBots as $baleBot) {
            $baleApi = new BaleApi($baleBot->bale_token);
            $baleApi->sendMessage(
                $baleBot->admin,
                $text
            );
        }
    }

    protected function setWebhook($blogId, $baleToken)
    {
        $url = BaleMessageService::new()->getWebhookUrl($blogId, $baleToken);

        $baleApi = new BaleApi($baleToken);
        $response = $baleApi->setWebhook($url);

        return ! empty($response['ok']);
    }
}
