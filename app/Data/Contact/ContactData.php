<?php

namespace App\Data\Contact;

use App\Data\Data;
use App\Enums\ContactTypeEnum;
use Illuminate\Validation\Rule;

class ContactData extends Data
{
    public function __construct(
        public ?int $id,
        public int $blog_id,
        public $contact_type,
        public $contact_key,
        public $contact_value,
        public $contact_link,
        public $contact_order
    ) {}

    public function rules($context)
    {
        $uniqueRule = Rule::unique('contacts')
            ->where('blog_id', $this->blog_id)
            ->where('contact_key', $this->contact_key);
        if ($this->id) {
            $uniqueRule = $uniqueRule->ignore($this->id);
        }

        return [
            'contact_type' => ['required', Rule::in(ContactTypeEnum::values())],
            'contact_key' => ['bail', 'required', 'max:2023', $uniqueRule],
            'contact_value' => ['required', 'max:1023'],
            'contact_link' => ['nullable'],
            'contact_order' => ['nullable', 'numeric'],
        ];
    }
}
