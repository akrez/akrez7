<?php

namespace App\Http\Resources\BaleBot;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class BaleBotResource extends JsonResource
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
            'bale_token' => $this->bale_token,
            'bale_bot_status' => $this->bale_bot_status ? $this->bale_bot_status->toResource() : null,
            'user_service' => $this->user_service,
            'notify_admin_on_invoice' => $this->notify_admin_on_invoice,
            'admin' => $this->admin,
        ];
    }
}
