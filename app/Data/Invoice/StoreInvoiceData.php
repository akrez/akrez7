<?php

namespace App\Data\Invoice;

use App\Data\Data;
use App\Enums\InvoiceStatusEnum;
use App\Services\PackageService;
use Illuminate\Validation\Rule;

class StoreInvoiceData extends Data
{
    public $packageApiResources = [];

    public function __construct(
        public int $blog_id,
        public ?array $invoice,
        public ?array $invoice_delivery,
        public ?array $invoice_items,
    ) {}

    public function rules($context)
    {
        return [
            'blog_id' => ['required', 'int'],
            //
            'invoice' => ['required', 'array'],
            'invoice.invoice_status' => ['required', Rule::in(InvoiceStatusEnum::values())],
            'invoice.invoice_description' => ['nullable', 'string'],
            //
            'invoice_delivery' => ['required', 'array'],
            'invoice_delivery.name' => ['required', 'string', 'max:128'],
            'invoice_delivery.mobile' => ['required', 'regex:/^09[0-9]{9,15}$/'],
            'invoice_delivery.phone' => ['nullable', 'regex:/^0[0-9]{8,23}$/'],
            'invoice_delivery.city' => ['required', 'string'],
            'invoice_delivery.address' => ['required', 'string'],
            'invoice_delivery.postal_code' => ['nullable', 'regex:/^\d{10}$/'],
            'invoice_delivery.lat' => ['nullable', 'regex:/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/'],
            'invoice_delivery.lng' => ['nullable', 'regex:/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/'],
            'invoice_delivery.invoice_delivery_description' => ['nullable', 'string'],
            //
            'invoice_items' => ['required', 'array'],
            'invoice_items.*.cnt' => ['required', 'integer', 'min:1'],
            'invoice_items.*.package_id' => ['required', 'distinct', function ($attribute, $value, $fail) {
                if ($value !== null) {
                    $packageApiResourceResult = PackageService::new()->getApiResource($this->blog_id, $value);
                    if ($packageApiResourceResult->isSuccessful()) {
                        $this->packageApiResources[$value] = $packageApiResourceResult->getData('package');
                    } else {
                        $fail(__('validation.exists', [
                            'Attribute' => __('validation.attributes.package_id'),
                        ]));
                    }
                }
            }],
        ];
    }
}
