<?php

namespace App\Http\Resources\Invoice;

use App\Http\Resources\InvoiceDelivery\InvoiceDeliveryResource;
use App\Http\Resources\InvoiceItem\InvoiceItemCollection;
use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class InvoiceResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'invoice_uuid' => $this->invoice_uuid,
            'invoice_status' => $this->invoice_status ? $this->invoice_status->toResource() : null,
            'invoice_description' => $this->invoice_params['invoice_description'] ?? null,
            'present_info' => $this->invoice_params['present_info'] ?? null,
            'created_at' => $this->formatCarbonDateTime($this->created_at),
            'updated_at' => $this->formatCarbonDateTime($this->updated_at),
            'invoiceDelivery' => $this->whenLoaded(
                'invoiceDelivery',
                $this->invoiceDelivery ? (new InvoiceDeliveryResource($this->invoiceDelivery))->toArr($request) : null,
            ),
            'invoiceItems' => $this->whenLoaded(
                'invoiceItems',
                (new InvoiceItemCollection($this->invoiceItems))->toArr($request),
            ),
        ];
    }
}

// 'natoos_level' => ($this->natoos_level_id ? new NatoosLevelResource($this->natoosLevel) : null),
// 'cefr' => ($this->cefr_id ? new CefrResource($this->cefr) : null),
