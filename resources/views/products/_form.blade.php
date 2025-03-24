<x-form :method="isset($product) ? 'PUT' : 'POST'" :action="isset($product) ? route('products.update', ['id' => $product['id']]) : route('products.store')">

    <x-input name="code" :errors="$errors" :value="isset($product) ? $product['code'] : ''" />
    <x-input name="name" :errors="$errors" :value="isset($product) ? $product['name'] : ''" />
    <x-input name="product_status" :errors="$errors" :value="isset($product) ? \Arr::get($product, 'product_status.value') : ''" type="select" :options="\App\Enums\ProductStatusEnum::toArray()" :label="__('validation.attributes.status')" />
    <x-input name="product_order" :errors="$errors" :value="isset($product) ? $product['product_order'] : ''" />

    <x-button-submit name="submit" :errors="$errors" :class="isset($product) ? 'btn-primary' : 'btn-success'">
        {{ isset($product) ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
