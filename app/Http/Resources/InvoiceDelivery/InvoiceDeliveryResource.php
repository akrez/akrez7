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
            'postal_code' => $this->delivery_params['postal_code'] ?? null,
            'city' => $this->delivery_params['city'] ?? null,
            'address' => $this->delivery_params['address'] ?? null,
            'lat' => $this->delivery_params['lat'] ?? null,
            'lng' => $this->delivery_params['lng'] ?? null,
            'invoice_delivery_description' => $this->delivery_params['invoice_delivery_description'] ?? null,
            'created_at' => $this->formatCarbonDateTime($this->created_at),
            'updated_at' => $this->formatCarbonDateTime($this->updated_at),
        ];
    }
}
