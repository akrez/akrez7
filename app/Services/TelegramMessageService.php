<?php

namespace App\Services;

use App\Data\TelegramMessage\StoreTelegramMessageData;
use App\Enums\TelegramMessageProcessStatusEnum;
use App\Models\TelegramMessage;
use App\Support\ApiResponse;
use App\Support\Arr;
use App\Support\TelegramApi;
use Illuminate\Support\Str;

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
        return route('api.telegram_messages.webhook', [
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

        $telegramMessage = TelegramMessage::create([
            'blog_id' => $storeTelegramMessageData->blog_id,
            'telegram_token' => $storeTelegramMessageData->telegram_token,
            'content_json' => (array) json_decode($storeTelegramMessageData->content_json, true),
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
        $this->updateStatus($telegramMessage, TelegramMessageProcessStatusEnum::PROCESSING, 300);

        $updateId = Arr::get($telegramMessage->content_json, 'update_id');
        $chatId = Arr::get($telegramMessage->content_json, 'message.chat.id');
        $messageText = Arr::get($telegramMessage->content_json, 'message.text');
        if (empty($updateId) || empty($chatId) || empty($messageText)) {
            return $this->updateStatus($telegramMessage, TelegramMessageProcessStatusEnum::NOT_VALID, 403);
        }

        $isBot = Arr::get($telegramMessage->content_json, 'message.from.is_bot');
        if ($isBot) {
            return $this->updateStatus($telegramMessage, TelegramMessageProcessStatusEnum::IS_BOT, 403);
        }

        $telegramBotResponse = TelegramBotService::new()->getApiResourceByTelegramToken(
            $telegramMessage->blog_id,
            $telegramMessage->telegram_token
        );
        if (! $telegramBotResponse->isSuccessful()) {
            return $this->updateStatus($telegramMessage, TelegramMessageProcessStatusEnum::BOT_NOT_FOUND, 404);
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
            return $this->updateStatus($telegramMessage, TelegramMessageProcessStatusEnum::ERROR_ON_UPDATE, 500);
        }

        $apiResponse = PresentService::new()->getCachedApiResponse($telegramMessage->blog_id, request())->getData();

        $telegramApi = new TelegramApi($telegramBotResource['telegram_token']);

        if ($telegramMessage->message_text === static::CONTACT_US) {
            $this->messageContactUs($telegramApi, $telegramMessage, $apiResponse);
        } elseif (Str::startsWith($telegramMessage->message_text, static::CATEGORY_PREFIX)) {
            $this->messageCategory($telegramApi, $telegramMessage, $apiResponse);
        } else {
            $this->messageDefault($telegramApi, $telegramMessage, $apiResponse);
        }

        return $this->updateStatus($telegramMessage, TelegramMessageProcessStatusEnum::OK, 200);
    }

    protected function messageContactUs(TelegramApi $telegramApi, TelegramMessage $telegramMessage, $apiResponse)
    {
        $contacts = Arr::get($apiResponse, 'contacts', []);

        $text = [];
        foreach ($contacts as $contactUs) {
            $text[] = '<b>'.$contactUs['contact_key'].'</b>'.' '.$contactUs['contact_value'];
        }

        return $telegramApi->sendMessage(
            $telegramMessage->chat_id,
            implode("\n", $text),
            $this->getReplyMarkup($apiResponse)
        );
    }

    protected function messageCategory(TelegramApi $telegramApi, TelegramMessage $telegramMessage, $apiResponse)
    {
        $products = Arr::get($apiResponse, 'products', []);

        $filterText = Str::of($telegramMessage->message_text)->chopStart(static::CATEGORY_PREFIX)->value();

        $filteredProducts = collect($products)->filter(function ($product) use ($filterText) {
            return in_array($filterText, $product['product_tags']);
        });

        $this->filterProducts(
            $telegramApi,
            $telegramMessage,
            $apiResponse,
            $filteredProducts->toArray(),
            'محصول با دسته بندی'.'<b>'.$filterText.'</b>'.'یافت نشد'
        );
    }

    protected function messageDefault(TelegramApi $telegramApi, TelegramMessage $telegramMessage, $apiResponse)
    {
        $products = Arr::get($apiResponse, 'products', []);

        $filterText = $telegramMessage->message_text;

        $filteredProducts = collect($products)->filter(function ($product) use ($filterText) {
            return Str::contains($product['name'], $filterText, true);
        });

        $this->filterProducts(
            $telegramApi,
            $telegramMessage,
            $apiResponse,
            $filteredProducts->toArray(),
            'محصول با عنوانی که شامل'.'<b>'.$filterText.'</b>'.'باشد یافت نشد'
        );
    }

    protected function filterProducts(TelegramApi $telegramApi, $telegramMessage, $apiResponse, $products, $notFoundMessage)
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
                    $telegramApi->sendMediaGroup(
                        $telegramMessage->chat_id,
                        $medias,
                        $this->getReplyMarkup($apiResponse)
                    );
                } else {
                    $telegramApi->sendMessage(
                        $telegramMessage->chat_id,
                        implode("\n", $caption),
                        $this->getReplyMarkup($apiResponse)
                    );
                }
            }
        } else {
            $telegramApi->sendMessage(
                $telegramMessage->chat_id,
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

    protected function updateStatus(TelegramMessage $telegramMessage, TelegramMessageProcessStatusEnum $processStatus, int $statusCode)
    {
        $telegramMessage->update(['process_status' => $processStatus]);

        return ApiResponse::new($statusCode)->data([
            'telegramMessage' => $telegramMessage,
        ]);
    }
}
