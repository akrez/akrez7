<?php

namespace App\Services;

use App\Data\BaleMessage\StoreBaleMessageData;
use App\Enums\BaleMessageProcessStatusEnum;
use App\Models\BaleMessage;
use App\Support\ApiResponse;
use App\Support\Arr;
use App\Support\BaleApi;
use Illuminate\Support\Str;

class BaleMessageService
{
    const CATEGORY_PREFIX = '🗂 | ';

    const CONTACT_US = '☎️ | ارتباط با ما';

    public static function new()
    {
        return app(self::class);
    }

    public function getWebhookUrl($blogId, $baleToken)
    {
        return route('api.bale_messages.webhook', [
            'blog_id' => $blogId,
            'bale_token' => $baleToken,
        ]);
    }

    public function webhook(StoreBaleMessageData $storeBaleMessageData)
    {
        $storeResponse = $this->store($storeBaleMessageData);
        if (! $storeResponse->isSuccessful()) {
            return $storeResponse->input($storeBaleMessageData);
        }

        $processResponse = $this->process($storeResponse->getData('baleMessage'));
        if (! $processResponse->isSuccessful()) {
            return $processResponse->input($storeBaleMessageData);
        }
    }

    protected function store(StoreBaleMessageData $storeBaleMessageData)
    {
        $validation = $storeBaleMessageData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return ApiResponse::new(422)->errors($validation->errors());
        }

        $baleMessage = BaleMessage::create([
            'blog_id' => $storeBaleMessageData->blog_id,
            'bale_token' => $storeBaleMessageData->bale_token,
            'content_json' => (array) json_decode($storeBaleMessageData->content_json, true),
        ]);

        if (! $baleMessage) {
            return ApiResponse::new(500);
        }

        return ApiResponse::new(201)->data([
            'baleMessage' => $baleMessage,
        ]);
    }

    protected function process(BaleMessage $baleMessage)
    {
        $this->updateStatus($baleMessage, BaleMessageProcessStatusEnum::PROCESSING, 300);

        $updateId = Arr::get($baleMessage->content_json, 'update_id');
        $chatId = Arr::get($baleMessage->content_json, 'message.chat.id');
        $messageText = Arr::get($baleMessage->content_json, 'message.text');
        if (empty($updateId) || empty($chatId) || empty($messageText)) {
            return $this->updateStatus($baleMessage, BaleMessageProcessStatusEnum::NOT_VALID, 403);
        }

        $isBot = Arr::get($baleMessage->content_json, 'message.from.is_bot');
        if ($isBot) {
            return $this->updateStatus($baleMessage, BaleMessageProcessStatusEnum::IS_BOT, 403);
        }

        $baleBotResponse = BaleBotService::new()->getApiResourceByBaleToken(
            $baleMessage->blog_id,
            $baleMessage->bale_token
        );
        $baleBotResource = $baleBotResponse->getData('baleBot');
        if (! $baleBotResponse->isSuccessful() || ! $baleBotResource) {
            return $this->updateStatus($baleMessage, BaleMessageProcessStatusEnum::BOT_NOT_FOUND, 404);
        }

        $wasUpdated = $baleMessage->update([
            'blog_id' => $baleBotResource['blog_id'],
            'bot_id' => $baleBotResource['id'],
            'update_id' => $updateId,
            'chat_id' => $chatId,
            'message_text' => $messageText,
        ]);
        if (! $wasUpdated) {
            return $this->updateStatus($baleMessage, BaleMessageProcessStatusEnum::ERROR_ON_UPDATE, 500);
        }

        if (! $baleBotResource['user_service']) {
            return $this->updateStatus($baleMessage, BaleMessageProcessStatusEnum::OK, 200);
        }

        $apiResponse = PresentService::new()->getCachedApiResponse($baleMessage->blog_id, request())->getData();

        $baleApi = new BaleApi($baleBotResource['bale_token']);

        if ($baleMessage->message_text === static::CONTACT_US) {
            $this->messageContactUs($baleApi, $baleMessage, $apiResponse);
        } elseif (Str::startsWith($baleMessage->message_text, static::CATEGORY_PREFIX)) {
            $this->messageCategory($baleApi, $baleMessage, $apiResponse);
        } else {
            $this->messageDefault($baleApi, $baleMessage, $apiResponse);
        }

        return $this->updateStatus($baleMessage, BaleMessageProcessStatusEnum::OK, 200);
    }

    protected function messageContactUs(BaleApi $baleApi, BaleMessage $baleMessage, $apiResponse)
    {
        $contacts = Arr::get($apiResponse, 'contacts', []);

        $text = [];
        foreach ($contacts as $contactUs) {
            $text[] = '<b>'.$contactUs['contact_key'].'</b>'.' '.$contactUs['contact_value'];
        }

        return $baleApi->sendMessage(
            $baleMessage->chat_id,
            implode("\n", $text),
            $this->getReplyMarkup($apiResponse)
        );
    }

    protected function messageCategory(BaleApi $baleApi, BaleMessage $baleMessage, $apiResponse)
    {
        $products = Arr::get($apiResponse, 'products', []);

        $filterText = Str::of($baleMessage->message_text)->chopStart(static::CATEGORY_PREFIX)->value();

        $filteredProducts = collect($products)->filter(function ($product) use ($filterText) {
            return in_array($filterText, $product['product_tags']);
        });

        $this->filterProducts(
            $baleApi,
            $baleMessage,
            $apiResponse,
            $filteredProducts->toArray(),
            'محصول با دسته بندی'.'<b>'.$filterText.'</b>'.'یافت نشد'
        );
    }

    protected function messageDefault(BaleApi $baleApi, BaleMessage $baleMessage, $apiResponse)
    {
        $products = Arr::get($apiResponse, 'products', []);

        $filterText = $baleMessage->message_text;

        $filteredProducts = collect($products)->filter(function ($product) use ($filterText) {
            return Str::contains($product['name'], $filterText, true);
        });

        $this->filterProducts(
            $baleApi,
            $baleMessage,
            $apiResponse,
            $filteredProducts->toArray(),
            'محصول با عنوانی که شامل'.'<b>'.$filterText.'</b>'.'باشد یافت نشد'
        );
    }

    protected function filterProducts(BaleApi $baleApi, $baleMessage, $apiResponse, $products, $notFoundMessage)
    {
        if ($products) {
            foreach ($products as $product) {
                $caption = ['<b>'.$product['name'].'</b>'];

                if ($product['product_properties']) {
                    $caption[] = '';
                    foreach ($product['product_properties'] as $productProperty) {
                        if ($productProperty['property_values']) {
                            $caption[] = '<b>'.$productProperty['property_key'].'</b>'.' '.implode(', ', $productProperty['property_values']);
                        }
                    }
                }

                if ($product['galleries']['product_image']) {
                    $medias = [];
                    foreach ($product['galleries']['product_image'] as $productImageKey => $productImage) {
                        $medias[$productImageKey] = [
                            'type' => 'photo',
                            'media' => $productImage['url'],
                        ];
                        if ($caption) {
                            $medias[$productImageKey]['caption'] = implode("\n", $caption);
                            $medias[$productImageKey]['parse_mode'] = 'HTML';
                            $caption = [];
                        }
                    }
                    $baleApi->sendMediaGroup(
                        $baleMessage->chat_id,
                        $medias,
                        $this->getReplyMarkup($apiResponse)
                    );
                } else {
                    $baleApi->sendMessage(
                        $baleMessage->chat_id,
                        implode("\n", $caption),
                        $this->getReplyMarkup($apiResponse)
                    );
                }
            }
        } else {
            $baleApi->sendMessage(
                $baleMessage->chat_id,
                $notFoundMessage,
                $this->getReplyMarkup($apiResponse)
            );
        }
    }

    protected function getReplyMarkup($apiResponse)
    {
        $products = Arr::get($apiResponse, 'products', []);

        $categories = collect($products)->pluck('product_tags')
            ->flatten()
            ->unique()
            ->sort()
            ->toArray();

        $keyboard = collect($categories)->map(function ($tag) {
            return [
                'text' => static::CATEGORY_PREFIX.$tag,
            ];
        })->toArray();

        return [
            'parse_mode' => 'HTML',
            'reply_markup' => json_encode([
                'resize_keyboard' => true,
                'one_time_keyboard' => true,
                'keyboard' => array_merge([
                    [
                        [
                            'text' => static::CONTACT_US,
                        ],
                    ],
                ], array_chunk($keyboard, 2)),
            ]),
        ];
    }

    protected function updateStatus(BaleMessage $baleMessage, BaleMessageProcessStatusEnum $processStatus, int $statusCode)
    {
        $baleMessage->update(['process_status' => $processStatus]);

        return ApiResponse::new($statusCode)->data([
            'baleMessage' => $baleMessage,
        ]);
    }
}
