<?php

namespace App\Http\Resources\InvoiceItem;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class InvoiceItemResource extends JsonResource
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
            'cnt' => $this->cnt,
            'price' => $this->price,
            'package_id' => $this->package_id,
            'invoice_id' => $this->invoice_id,
            'created_at' => $this->formatCarbonDateTime($this->created_at),
            'updated_at' => $this->formatCarbonDateTime($this->updated_at),
        ];
    }
}
