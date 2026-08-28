<?php

namespace App\Models;

use App\Enums\BaleBotStatusEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class BaleBot
 *
 * @property int $id
 * @property BaleBotStatusEnum $bale_bot_status
 * @property string $bale_token
 * @property bool $user_service
 * @property bool $notify_admin_on_invoice
 * @property string|null $admin
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class BaleBot extends Model
{
    protected $table = 'bale_bots';

    protected $casts = [
        'blog_id' => 'int',
        'bale_bot_status' => BaleBotStatusEnum::class,
        'user_service' => 'boolean',
        'notify_admin_on_invoice' => 'boolean',
    ];

    protected $hidden = [
        'bale_token',
    ];

    protected $fillable = [
        'bale_bot_status',
        'bale_token',
        'user_service',
        'notify_admin_on_invoice',
        'admin',
        'blog_id',
    ];

    public function scopeDefaultOrder(Builder $query): void
    {
        $query = $query
            ->orderBy('created_at', 'ASC');
    }
}
