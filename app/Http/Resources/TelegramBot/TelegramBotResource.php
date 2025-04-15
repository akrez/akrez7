<?php

namespace App\Http\Resources\TelegramBot;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class TelegramBotResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'blog_id' => $this->blog_id,
            'telegram_token' => $this->telegram_token,
            'telegram_bot_status' => $this->telegram_bot_status ? $this->telegram_bot_status->toResource() : null,
        ];
    }
}
