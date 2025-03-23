<?php

namespace App\Data\Contact;

use App\Data\Data;
use App\Enums\ContactTypeEnum;
use Illuminate\Validation\Rule;

class ContactData extends Data
{
    public function __construct(
        public int $blog_id,
        public $contact_type,
        public $contact_value,
        public $contact_link,
        public $contact_order
    ) {}

    public function rules($context)
    {
        return [
            'contact_type' => ['required', Rule::in(ContactTypeEnum::values())],
            'contact_value' => ['required', 'max:1023'],
            'contact_link' => ['nullable'],
            'contact_order' => ['nullable', 'numeric'],
        ];
    }
}
