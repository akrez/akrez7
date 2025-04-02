@php
    $isVertical = isset($isVertical) && $isVertical;
@endphp

<x-form :method="isset($package) ? 'PUT' : 'POST'" :action="isset($package)
    ? route('products.packages.update', ['product_id' => $product['id'], 'id' => $package['id']])
    : route('products.packages.store', ['product_id' => $product['id']])">
    @if ($isVertical)
        <div class="row">
    @endif
    @if (!isset($package))
        <x-input :md="isset($package) ? 12 : 3" :row="!$isVertical" name="price" :errors="$errors" :value="isset($package) ? $package['price'] : ''" />
        <x-input :md="isset($package) ? 12 : 3" :row="!$isVertical" name="guaranty" :errors="$errors" :value="isset($package) ? $package['guaranty'] : ''" />
        <x-input :md="isset($package) ? 12 : 3" :row="!$isVertical" name="color_id" :errors="$errors" :value="isset($package) ? $package['color_id'] : ''"
            type="select" :options="['' => ''] + collect($colorsIdArray)->pluck('name', 'id')->toArray()" />
    @endif
    <x-input :md="isset($package) ? 12 : 3" :row="!$isVertical" name="package_status" :errors="$errors" :value="isset($package) ? $package['package_status']['value'] : ''"
        type="select" :label="__('validation.attributes.status')" :options="\App\Enums\PackageStatusEnum::toArray()" />
    @if ($isVertical)
        </div>
        <div class="row">
    @endif
    @if (!isset($package))
        <x-input :md="!$isVertical ? 12 : 6" :row="!$isVertical" name="description" :errors="$errors" :value="isset($package) ? $package['description'] : ''" />
    @endif
    @if ($isVertical)
        </div>
    @endif
    <x-button-submit :md="isset($package) ? 12 : 3" name="submit" :errors="$errors" :class="isset($package) ? 'btn-primary' : 'btn-success'">
        {{ isset($package) ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
