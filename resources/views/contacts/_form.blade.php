@php
    $isVertical = isset($isVertical) && $isVertical;
@endphp

<x-form :method="isset($contact) ? 'PUT' : 'POST'" :action="isset($contact) ? route('contacts.update', ['id' => $contact['id']]) : route('contacts.store')">
    @if ($isVertical)
        <div class="row">
    @endif
    <x-input :md="3" :row="!$isVertical" name="contact_type" :errors="$errors" :value="isset($contact) ? $contact['contact_type'] : ''" type="select"
        :options="\App\Enums\ContactTypeEnum::toArray()" />
    <x-input :md="3" :row="!$isVertical" name="contact_key" :errors="$errors" :value="isset($contact) ? $contact['contact_key'] : ''" />
    <x-input :md="3" :row="!$isVertical" name="contact_value" :errors="$errors" :value="isset($contact) ? $contact['contact_value'] : ''" />
    <x-input :md="3" :row="!$isVertical" name="contact_link" :errors="$errors" :value="isset($contact) ? $contact['contact_link'] : ''" />
    <x-input :md="3" :row="!$isVertical" name="contact_order" :errors="$errors" :value="isset($contact) ? $contact['contact_order'] : ''" />
    @if ($isVertical)
        </div>
    @endif
    <x-button-submit :md="3" name="submit" :errors="$errors" :class="isset($contact) ? 'btn-primary' : 'btn-success'">
        {{ isset($contact) ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
