<?php

namespace App\Data\Invoice;

use App\Data\Data;
use App\Enums\InvoiceStatusEnum;
use Illuminate\Validation\Rule;

class IndexInvoiceData extends Data
{
    public function __construct(
        public int $blog_id,
        public ?array $invoice,
        public ?array $invoice_delivery,
    ) {
        $this->blog_id = $blog_id;
    }

    public function rules($context)
    {
        return [
            'blog_id' => ['required', 'int'],
            //
            'invoice' => ['nullable', 'array'],
            'invoice.invoice_status' => ['nullable', Rule::in(InvoiceStatusEnum::values())],
            //
            'invoice_delivery' => ['nullable', 'array'],
            'invoice_delivery.name' => ['nullable', 'string', 'max:128'],
            'invoice_delivery.mobile' => ['nullable', 'string', 'max:16'],
        ];
    }
}
