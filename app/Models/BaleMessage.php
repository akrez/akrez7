<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class BaleMessage
 *
 * @property int $id
 * @property int|null $blog_id
 * @property string $bale_token
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
class BaleMessage extends Model
{
    use SoftDeletes;

    protected $table = 'bale_messages';

    protected $casts = [
        'blog_id' => 'int',
        'content_json' => 'json',
        'bot_id' => 'int',
        'update_id' => 'int',
        'chat_id' => 'int',
    ];

    protected $hidden = [
        'bale_token',
    ];

    protected $fillable = [
        'blog_id',
        'bale_token',
        'content_json',
        'process_status',
        'bot_id',
        'update_id',
        'chat_id',
        'message_text',
    ];
}
