<?php

namespace App\Models;

use App\Enums\ContactTypeEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

/**
 * Class Contact
 *
 * @property int $id
 * @property ContactTypeEnum $contact_type
 * @property string|null $contact_key
 * @property string|null $contact_value
 * @property string|null $contact_link
 * @property float|null $contact_order
 * @property bool|null $presenter_visible
 * @property bool|null $invoice_visible
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Contact extends Model
{
    protected $table = 'contacts';

    protected $casts = [
        'contact_order' => 'float',
        'presenter_visible' => 'boolean',
        'invoice_visible' => 'boolean',
        'blog_id' => 'int',
        'contact_type' => ContactTypeEnum::class,
    ];

    protected $fillable = [
        'contact_type',
        'contact_key',
        'contact_value',
        'contact_link',
        'contact_order',
        'presenter_visible',
        'invoice_visible',
        'blog_id',
    ];

    protected static function boot()
    {
        parent::boot();
        static::blogUpdatedboot();
    }

    public function scopeDefaultOrder(Builder $query): void
    {
        $query = $query
            ->orderBy('contact_order', 'DESC')
            ->orderBy('created_at', 'ASC');
    }
}
