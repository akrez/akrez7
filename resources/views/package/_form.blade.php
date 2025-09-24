@php
    $isVertical = isset($isVertical) ? $isVertical : false;
    $packageId = isset($package) ? $package['id'] : null;
@endphp

<x-form :method="$packageId ? 'PUT' : 'POST'" :action="$packageId
    ? route('products.packages.update', ['product_id' => $product['id'], 'id' => $package['id']])
    : route('products.packages.store', ['product_id' => $product['id']])" :id="'package-upset-' . $packageId">
    <div class="row">
        <x-input :row="false" :md="3" name="price" :errors="$errors" :value="$packageId ? $package['price'] : ''" />
        <x-input :row="false" :md="3" name="show_price" :errors="$errors" :value="$packageId ? $package['show_price'] : '1'" type="select"
            :options="['' => __('No'), '1' => __('Yes')]" />
        <x-input :row="false" :md="3" name="package_status" :errors="$errors" :value="$packageId ? $package['package_status']['value'] : ''"
            type="select" :label="__('validation.attributes.status')" :options="\App\Enums\PackageStatusEnum::toArray()" />
    </div>
    <div class="row">
        <x-input :row="false" :disabled="$packageId" :md="3" name="unit" :errors="$errors"
            :value="$packageId ? $package['unit'] : ''" />
        <x-input :row="false" :disabled="$packageId" :md="3" name="color_id" :errors="$errors"
            :value="$packageId ? $package['color_id'] : ''" type="select" :options="['' => ''] + collect($colorsIdArray)->pluck('name', 'id')->toArray()" />
        <x-input :row="false" :disabled="$packageId" :md="3" name="guaranty" :errors="$errors"
            :value="$packageId ? $package['guaranty'] : ''" />
        <x-input :row="false" :disabled="$packageId" :md="3" name="description" :errors="$errors"
            :value="$packageId ? $package['description'] : ''" />
    </div>
    <div class="row">
        <x-button-submit :form="'package-upset-' . $packageId" :row="false" :md="3" name="submit" :errors="$errors"
            :class="$packageId ? 'btn-primary' : 'btn-success'">
            {{ $packageId ? __('Edit') : __('Create') }}
        </x-button-submit>
    </div>
</x-form>
