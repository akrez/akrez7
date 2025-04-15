<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TelegramMessage
 *
 * @property int $id
 * @property int|null $blog_id
 * @property string $telegram_token
 * @property array $content_json
 * @property string|null $process_status
 * @property int|null $bot_id
 * @property int|null $update_id
 * @property int|null $chat_id
 * @property string|null $message_text
 * @property string|null $deleted_at
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class TelegramMessage extends Model
{
    use SoftDeletes;

    protected $table = 'telegram_messages';

    protected $casts = [
        'blog_id' => 'int',
        'content_json' => 'json',
        'bot_id' => 'int',
        'update_id' => 'int',
        'chat_id' => 'int',
    ];

    protected $hidden = [
        'telegram_token',
    ];

    protected $fillable = [
        'blog_id',
        'telegram_token',
        'content_json',
        'process_status',
        'bot_id',
        'update_id',
        'chat_id',
        'message_text',
    ];
}
