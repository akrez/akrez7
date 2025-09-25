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
            'invoice_status' => $this->invoice_status ? $this->invoice_status->toResource() : null,
            'invoice_description' => $this->invoice_params['invoice_description'] ?? null,
            'created_at' => $this->formatCarbonDateTime($this->created_at),
            'updated_at' => $this->formatCarbonDateTime($this->updated_at),
            'invoiceDelivery' => $this->whenLoaded(
                'invoiceDelivery',
                $this->invoiceDelivery ? new InvoiceDeliveryResource($this->invoiceDelivery) : null,
            ),
            'invoiceItems' => $this->whenLoaded(
                'invoiceItems',
                new InvoiceItemCollection($this->invoiceItems),
            ),
        ];
    }
}

// 'natoos_level' => ($this->natoos_level_id ? new NatoosLevelResource($this->natoosLevel) : null),
// 'cefr' => ($this->cefr_id ? new CefrResource($this->cefr) : null),
