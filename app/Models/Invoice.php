<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use App\Enums\InvoiceStatusEnum;
use Carbon\Carbon;

/**
 * Class Invoice
 *
 * @property int $id
 * @property string $invoice_status
 * @property array $invoice_params
 * @property int $blog_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 */
class Invoice extends Model
{
    protected $table = 'invoices';

    protected $casts = [
        'invoice_params' => 'json',
        'blog_id' => 'int',
        'invoice_status' => InvoiceStatusEnum::class,
    ];

    protected $fillable = [
        'invoice_status',
        'invoice_params',
        'blog_id',
    ];
}
