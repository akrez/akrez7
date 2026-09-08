@php
    $isVertical = !empty($isVertical);
    $categoryOptions =
        ['' => ''] +
        collect($categories ?? [])
            ->mapWithKeys(fn($category) => [$category['id'] => $category['name']])
            ->all();
@endphp

<x-form :method="isset($category_property) ? 'PUT' : 'POST'" :action="isset($category_property)
    ? route('category_properties.update', ['id' => $category_property['id']])
    : route('category_properties.store')">
    @if ($isVertical)
        <div class="row">
    @endif
    <x-input :md="$isVertical ? 3 : 12" :row="!$isVertical" name="category_id" :errors="$errors" :value="isset($category_property) ? $category_property['category_id'] : ''" type="select"
        :options="$categoryOptions" />
    <x-input :md="$isVertical ? 3 : 12" :row="!$isVertical" name="name" :errors="$errors" :value="isset($category_property) ? $category_property['name'] : ''" />
    <x-input :md="$isVertical ? 3 : 12" :row="!$isVertical" name="filter_type" :errors="$errors" :value="isset($category_property) ? \Arr::get($category_property, 'filter_type.value') : ''"
        type="select" :options="['' => ''] + \App\Enums\CategoryPropertyTypeEnum::toArray()" />
    <x-input :md="$isVertical ? 3 : 12" :row="!$isVertical" name="unit" :errors="$errors" :value="isset($category_property) ? $category_property['unit'] : ''" />
    @if ($isVertical)
        </div>
    @endif
    <x-button-submit :md="$isVertical ? 3 : 12" name="submit" :errors="$errors" :class="isset($category_property) ? 'btn-primary' : 'btn-success'">
        {{ isset($category_property) ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
