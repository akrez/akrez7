<?php

namespace App\Services;

use App\Data\Contact\StoreContactData;
use App\Data\Contact\UpdateContactData;
use App\Http\Resources\Contact\ContactCollection;
use App\Http\Resources\Contact\ContactResource;
use App\Models\Contact;
use App\Support\ApiResponse;
use App\Support\WebResponse;

class ContactService extends Service
{
    public static function new()
    {
        return app(self::class);
    }

    public function getApiResource(int $blogId): ApiResponse
    {
        $model = $this->getLatestApiQuery($blogId)
            ->first();

        return ApiResponse::new(200)->data([
            'contact' => (new ContactResource($model))->toArr(),
        ]);
    }

    public function getApiCollection(int $blogId): ApiResponse
    {
        $models = $this->getLatestApiQuery($blogId)
            ->get();

        return ApiResponse::new(200)->data([
            'contacts' => (new ContactCollection($models))->toArr(),
        ]);
    }

    protected function getLatestBaseQuery($blogId)
    {
        return Contact::query()
            ->where('blog_id', $blogId)
            ->defaultOrder();
    }

    public function getLatestContacts(int $blogId)
    {
        $contacts = $this->getLatestBlogQuery($blogId)->get();

        return WebResponse::new()->data([
            'contacts' => (new ContactCollection($contacts))->toArr(),
        ]);
    }

    public function storeContact(StoreContactData $storeContactData)
    {
        $webResponse = WebResponse::new()->input($storeContactData);

        $validation = $storeContactData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $contact = Contact::create([
            'contact_type' => $storeContactData->contact_type,
            'contact_value' => $storeContactData->contact_value,
            'contact_link' => $storeContactData->contact_link,
            'contact_order' => $storeContactData->contact_order,
            'blog_id' => $storeContactData->blog_id,
        ]);
        if (! $contact) {
            return $webResponse->status(500);
        }

        return $webResponse->status(201)->data($contact)->message(__(':name is created successfully', [
            'name' => __('Contact'),
        ]));
    }

    public function getContact(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $contact = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $contact) {
            return $webResponse->status(404);
        }

        return WebResponse::new()->data([
            'contact' => (new ContactResource($contact))->toArr(),
        ]);
    }

    public function updateContact(UpdateContactData $updateContactData)
    {
        $webResponse = WebResponse::new()->input($updateContactData);

        $validation = $updateContactData->validate();
        if ($validation->errors()->isNotEmpty()) {
            return $webResponse->status(422)->errors($validation->errors());
        }

        $contact = $this->getLatestBlogQuery($updateContactData->blog_id)->where('id', $updateContactData->id)->first();
        if (! $contact) {
            return $webResponse->status(404);
        }

        $contact->update([
            'contact_type' => $updateContactData->contact_type,
            'contact_value' => $updateContactData->contact_value,
            'contact_link' => $updateContactData->contact_link,
            'contact_order' => $updateContactData->contact_order,
            'blog_id' => $updateContactData->blog_id,
        ]);
        if (! $contact->save()) {
            return $webResponse->status(500);
        }

        return $webResponse
            ->status(201)
            ->data(['contact' => (new ContactResource($contact))->toArr()])
            ->message(__(':name is updated successfully', [
                'name' => $contact->name,
            ]));
    }

    public function destroyContact(int $blogId, int $id)
    {
        $webResponse = WebResponse::new();

        $contact = $this->getLatestBlogQuery($blogId)->where('id', $id)->first();
        if (! $contact) {
            return $webResponse->status(404);
        }

        if (! $contact->delete()) {
            return $webResponse->status(500);
        }

        return WebResponse::new(200)->message(__(':name is deleted successfully', [
            'name' => $contact->contact_value,
        ]));
    }
}
