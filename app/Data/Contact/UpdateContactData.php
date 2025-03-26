<?php

namespace App\Data\Contact;

class UpdateContactData extends ContactData
{
    public function rules($context)
    {
        return [
            'id' => ['required', 'integer'],
        ] + parent::rules($context);
    }
}
