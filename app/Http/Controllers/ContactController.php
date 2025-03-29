<?php

namespace App\Http\Controllers;

use App\Data\Contact\StoreContactData;
use App\Data\Contact\UpdateContactData;
use App\Services\ContactService;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function __construct(protected ContactService $contactService) {}

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $response = $this->contactService->getLatestContacts($this->blogId());

        return view('contacts.index', [
            'contacts' => $response->getData('contacts'),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('contacts.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $storeContactData = new StoreContactData(
            null,
            $this->blogId(),
            $request->contact_type,
            $request->contact_value,
            $request->contact_link,
            $request->contact_order
        );

        $response = $this->contactService->storeContact($storeContactData);

        return $response->successfulRoute(route('contacts.index'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $response = $this->contactService->getContact($this->blogId(), $id)->abortUnSuccessful();

        return view('contacts.edit', [
            'contact' => $response->getData('contact'),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, int $id)
    {
        $updateContactData = new UpdateContactData(
            $id,
            $this->blogId(),
            $request->contact_type,
            $request->contact_value,
            $request->contact_link,
            $request->contact_order
        );

        $response = $this->contactService->updateContact($updateContactData);

        return $response->successfulRoute(route('contacts.index'));
    }

    public function destroy(Request $request, int $id)
    {
        return $this->contactService->destroyContact($this->blogId(), $id);
    }
}
