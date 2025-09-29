<?php

namespace App\Http\Resources\InvoiceDelivery;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class InvoiceDeliveryResource extends JsonResource
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
            'name' => $this->name,
            'mobile' => $this->mobile,
            'phone' => $this->phone,
            'invoice_id' => $this->invoice_id,
            'postal_code' => $this->invoice_delivery_params['postal_code'] ?? null,
            'city' => $this->invoice_delivery_params['city'] ?? null,
            'address' => $this->invoice_delivery_params['address'] ?? null,
            'lat' => $this->invoice_delivery_params['lat'] ?? null,
            'lng' => $this->invoice_delivery_params['lng'] ?? null,
            'invoice_delivery_description' => $this->invoice_delivery_params['invoice_delivery_description'] ?? null,
            'created_at' => $this->formatCarbonDateTime($this->created_at),
            'updated_at' => $this->formatCarbonDateTime($this->updated_at),
        ];
    }
}
