<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class InvoiceDelivery
 *
 * @property int $id
 * @property string $name
 * @property string $mobile
 * @property string|null $phone
 * @property array $invoice_delivery_params
 * @property int $invoice_id
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class InvoiceDelivery extends Model
{
    protected $table = 'invoice_deliveries';

    protected $casts = [
        'invoice_delivery_params' => 'json',
        'invoice_id' => 'int',
        'blog_id' => 'int',
    ];

    protected $fillable = [
        'name',
        'mobile',
        'phone',
        'invoice_delivery_params',
        'invoice_id',
        'blog_id',
    ];
}
