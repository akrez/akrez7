<?php

namespace App\Models;

use App\Enums\TelegramMessageProcessStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TelegramMessage
 *
 * @property int $id
 * @property string $telegram_token
 * @property array $message_json
 * @property string|null $process_status
 * @property int|null $update_id
 * @property int|null $blog_id
 * @property int|null $bot_id
 * @property int|null $chat_id
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TelegramMessage extends Model
{
    use SoftDeletes;

    protected $table = 'telegram_messages';

    protected $casts = [
        'message_json' => 'json',
        'update_id' => 'int',
        'blog_id' => 'int',
        'bot_id' => 'int',
        'chat_id' => 'int',
        'process_status' => TelegramMessageProcessStatusEnum::class,
    ];

    protected $hidden = [
        'telegram_token',
    ];

    protected $fillable = [
        'telegram_token',
        'message_json',
        'process_status',
        'update_id',
        'blog_id',
        'bot_id',
        'chat_id',
    ];
}
