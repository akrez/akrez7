<x-form method="POST" :action="route('products.product_tags.store', ['product_id' => $product['id']])">
    <x-input name="tag_names" type="textarea" :errors="$errors" :value="$productTagsText" label="" :rows='max(5, substr_count($productTagsText, PHP_EOL) + 2)'
        :hints="[
            __('Separate :names using :characters characters', [
                'names' => __('Tags'),
                'characters' => implode(' ', \App\Services\ProductTagService::NAME_SEPARATORS),
            ]),
        ]" />
    <x-button-submit name="submit" :class="isset($productTagsText) && $productTagsText ? 'btn-primary' : 'btn-success'">
        {{ isset($productTagsText) && $productTagsText ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
