<x-form :method="isset($gallery) ? 'PUT' : 'POST'" :action="isset($gallery) ? route('galleries.update', ['id' => $gallery['id']]) : route('galleries.store')">
    @if (!isset($gallery))
    <x-input md="12" type="file" name="file" :errors="$errors" :label="__('validation.attributes.file')" />
    @endif
    <x-input md="12" type="select" name="is_selected" :errors="$errors" :label="__('validation.attributes.is_selected')" :value="isset($gallery) ? ($gallery['selected_at'] ? '1' : '') : ''" :options="['' => __('No'), '1' => __('Yes')]" />
    <x-input md="12" name="gallery_order" :errors="$errors" :label="__('validation.attributes.gallery_order')" :value="isset($gallery) ? $gallery['gallery_order'] : ''" />
    <input type="hidden" name="gallery_category" value="{{ $gallery_category }}" />
    <input type="hidden" name="gallery_type" value="{{ $gallery_type }}" />
    <input type="hidden" name="gallery_id" value="{{ $gallery_id }}" />
    <x-button-submit name="submit" md="12">
        {{ isset($gallery) ? __('Edit') : __('Upload') }}
    </x-button-submit>
</x-form>
