@php
    $isVertical = isset($isVertical) && $isVertical;
@endphp

<x-form :method="isset($category) ? 'PUT' : 'POST'" :action="isset($category) ? route('categories.update', ['id' => $category['id']]) : route('categories.store')">
    @if ($isVertical)
        <div class="row">
    @endif
    <x-input :md="$isVertical ? 3 : 12" :row="!$isVertical" name="name" :errors="$errors" :value="isset($category) ? $category['name'] : ''" />
    @if ($isVertical)
        </div>
    @endif
    <x-button-submit :md="$isVertical ? 3 : 12" name="submit" :errors="$errors" :class="isset($category) ? 'btn-primary' : 'btn-success'">
        {{ isset($category) ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
