<x-form method="POST" :action="route('products.product_properties.store', ['product_id' => $product['id']])">
    <x-input name="keys_values" type="textarea" :errors="$errors" :value="$productPropertiesText" label="" :rows='max(5, substr_count($productPropertiesText, PHP_EOL) + 2)'
        :hints="[
            __('Write each :name on one line', [
                'name' => __('Property'),
            ]),
            __('Separate :names using :characters characters', [
                'names' => __('validation.attributes.keys_values'),
                'characters' => implode(' ', \App\Services\ProductPropertyService::KEY_VALUES_SEPARATORS),
            ]),
        ]" />
    <x-button-submit name="submit" :class="isset($productPropertiesText) && $productPropertiesText ? 'btn-primary' : 'btn-success'">
        {{ isset($productPropertiesText) && $productPropertiesText ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
