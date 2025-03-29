<?php

namespace App\Services;

use App\Data\Contact\StoreContactData;
use App\Data\Contact\UpdateContactData;
use App\Http\Resources\Contact\ContactCollection;
use App\Http\Resources\Contact\ContactResource;
use App\Models\Contact;
use App\Support\ResponseBuilder;

class ContactService
{
    public static function new()
    {
        return app(self::class);
    }

    protected function getContactsQuery($blogId)
    {
        return Contact::query()->where('blog_id', $blogId);
    }

    public function getLatestContacts(int $blogId)
    {
        $contacts = $this->getContactsQuery($blogId)->get();

        return ResponseBuilder::new()->data([
            'contacts' => (new ContactCollection($contacts))->toArray(request()),
        ]);
    }

    public function storeContact(StoreContactData $storeContactData)
    {
        $responseBuilder = ResponseBuilder::new()->input($storeContactData);

        $validation = $storeContactData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $contact = Contact::create([
            'contact_type' => $storeContactData->contact_type,
            'contact_value' => $storeContactData->contact_value,
            'contact_link' => $storeContactData->contact_link,
            'contact_order' => $storeContactData->contact_order,
            'blog_id' => $storeContactData->blog_id,
        ]);
        if (! $contact) {
            return $responseBuilder->status(500);
        }

        return $responseBuilder->status(201)->data($contact)->message(__(':name is created successfully', [
            'name' => __('Contact'),
        ]));
    }

    public function getContact(int $blogId, int $id)
    {
        $responseBuilder = ResponseBuilder::new();

        $contact = $this->getContactsQuery($blogId)->where('id', $id)->first();
        if (! $contact) {
            return $responseBuilder->status(404);
        }

        return ResponseBuilder::new()->data([
            'contact' => (new ContactResource($contact))->toArr(request()),
        ]);
    }

    public function updateContact(UpdateContactData $updateContactData)
    {
        $responseBuilder = ResponseBuilder::new()->input($updateContactData);

        $validation = $updateContactData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $responseBuilder->status(422)->errors($validation->errors());
        }

        $contact = $this->getContactsQuery($updateContactData->blog_id)->where('id', $updateContactData->id)->first();
        if (! $contact) {
            return $responseBuilder->status(404);
        }

        $contact->update([
            'contact_type' => $updateContactData->contact_type,
            'contact_value' => $updateContactData->contact_value,
            'contact_link' => $updateContactData->contact_link,
            'contact_order' => $updateContactData->contact_order,
            'blog_id' => $updateContactData->blog_id,
        ]);
        if (! $contact->save()) {
            return $responseBuilder->status(500);
        }

        return $responseBuilder
            ->status(201)
            ->data(['contact' => (new ContactResource($contact))->toArr(request())])
            ->message(__(':name is updated successfully', [
                'name' => $contact->name,
            ]));
    }
}
