<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class TelegramMessage
 *
 * @property int $id
 * @property int|null $chat_id
 * @property array|null $message_json
 * @property string|null $processor
 * @property int $blog_id
 * @property int $bot_id
 * @property Carbon|null $responsed_at
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
        'chat_id' => 'int',
        'message_json' => 'json',
        'blog_id' => 'int',
        'bot_id' => 'int',
        'responsed_at' => 'datetime',
    ];

    protected $fillable = [
        'chat_id',
        'message_json',
        'processor',
        'blog_id',
        'bot_id',
        'responsed_at',
    ];
}
