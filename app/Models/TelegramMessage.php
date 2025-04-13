<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TelegramMessage
 *
 * @property int $id
 * @property string $telegram_token
 * @property array|null $message_json
 * @property string|null $process_status
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

    public $incrementing = false;

    protected $casts = [
        'id' => 'int',
        'message_json' => 'json',
        'blog_id' => 'int',
        'bot_id' => 'int',
        'chat_id' => 'int',
    ];

    protected $hidden = [
        'telegram_token',
    ];

    protected $fillable = [
        'telegram_token',
        'message_json',
        'process_status',
        'blog_id',
        'bot_id',
        'chat_id',
    ];
}
