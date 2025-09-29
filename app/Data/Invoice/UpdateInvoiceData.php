<?php

namespace App\Data\Invoice;

use App\Data\Data;
use App\Enums\InvoiceStatusEnum;
use Illuminate\Validation\Rule;

class UpdateInvoiceData extends Data
{
    public function __construct(
        public int $blog_id,
        public int $invoice_id,
        public $invoice,
    ) {}

    public function rules($context)
    {
        return [
            'blog_id' => ['required', 'int'],
            'invoice_id' => ['required', 'int'],
            'invoice' => ['required', 'array'],
            'invoice.invoice_status' => ['required', Rule::in(InvoiceStatusEnum::values())],
        ];
    }
}
