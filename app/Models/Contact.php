<?php

namespace App\Models;

use App\Enums\ContactTypeEnum;
use Carbon\Carbon;

/**
 * Class Contact
 *
 * @property int $id
 * @property ContactTypeEnum $contact_type
 * @property string|null $contact_value
 * @property string|null $contact_link
 * @property float|null $contact_order
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Contact extends Model
{
    protected $table = 'contacts';

    protected $casts = [
        'contact_order' => 'float',
        'blog_id' => 'int',
        'contact_type' => ContactTypeEnum::class,
    ];

    protected $fillable = [
        'contact_type',
        'contact_value',
        'contact_link',
        'contact_order',
        'blog_id',
    ];
}
