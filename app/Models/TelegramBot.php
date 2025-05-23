<?php

namespace App\Models;

use App\Enums\TelegramBotStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class TelegramBot
 *
 * @property int $id
 * @property TelegramBotStatusEnum $telegram_bot_status
 * @property string $telegram_token
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TelegramBot extends Model
{
    protected $table = 'telegram_bots';

    protected $casts = [
        'blog_id' => 'int',
        'telegram_bot_status' => TelegramBotStatusEnum::class,
    ];

    protected $hidden = [
        'telegram_token',
    ];

    protected $fillable = [
        'telegram_bot_status',
        'telegram_token',
        'blog_id',
    ];

    public function scopeDefaultOrder(Builder $query): void
    {
        $query = $query
            ->orderBy('created_at', 'ASC');
    }
}
