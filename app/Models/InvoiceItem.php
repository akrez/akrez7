<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class InvoiceItem
 *
 * @property int $id
 * @property float $price
 * @property int $cnt
 * @property int $package_id
 * @property int $invoice_id
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InvoiceItem extends Model
{
    protected $table = 'invoice_items';

    protected $casts = [
        'cnt' => 'int',
        'package_id' => 'int',
        'invoice_id' => 'int',
        'blog_id' => 'int',
        'price' => 'float',
    ];

    protected $fillable = [
        'cnt',
        'package_id',
        'invoice_id',
        'blog_id',
        'price',
    ];
}
