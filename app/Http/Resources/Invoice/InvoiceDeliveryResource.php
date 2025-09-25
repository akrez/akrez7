<?php

namespace App\Http\Resources\Invoice;

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
            'invoice_description' => $this->delivery_params['invoice_description'] ?? null,
            'created_at' => $this->formatCarbonDateTime($this->created_at),
            'updated_at' => $this->formatCarbonDateTime($this->updated_at),
        ];
    }
}
