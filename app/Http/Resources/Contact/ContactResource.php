<?php

namespace App\Http\Resources\Contact;

use App\Http\Resources\JsonResource;
use Illuminate\Http\Request;

class ContactResource extends JsonResource
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
            'contact_type' => $this->contact_type ? $this->contact_type->toResource() : null,
            'contact_value' => $this->contact_value,
            'contact_link' => $this->contact_link,
            'contact_order' => $this->contact_order,
            'created_at' => $this->formatCarbonDateTime($this->created_at),
            'updated_at' => $this->formatCarbonDateTime($this->updated_at),
        ];
    }
}
