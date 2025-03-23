<x-form :method="isset($contact) ? 'PUT' : 'POST'" :action="isset($contact) ? route('contacts.update', ['id' => $contact['id']]) : route('contacts.store')">

    <x-input name="contact_type" :errors="$errors" :value="isset($contact) ? $contact['contact_type'] : ''" type="select" :options="\App\Enums\ContactTypeEnum::toArray()" />
    <x-input name="contact_value" :errors="$errors" :value="isset($contact) ? $contact['contact_value'] : ''" />
    <x-input name="contact_link" :errors="$errors" :value="isset($contact) ? $contact['contact_link'] : ''" />
    <x-input name="contact_order" :errors="$errors" :value="isset($contact) ? $contact['contact_order'] : ''" />

    <x-button-submit name="submit" :errors="$errors" :class="isset($contact) ? 'btn-primary' : 'btn-success'">
        {{ isset($contact) ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
