@php
    $isVertical = isset($isVertical) && $isVertical;
@endphp

<x-form :method="isset($gallery) ? 'PUT' : 'POST'" :action="isset($gallery) ? route('galleries.update', ['id' => $gallery['id']]) : route('galleries.store')">
    @if ($isVertical)
        <div class="row">
    @endif
    @if (!isset($gallery))
        <x-input :md="$isVertical ? 3 : 12" :row="!$isVertical" type="file" name="file" :errors="$errors" :label="__('validation.attributes.file')" />
    @endif
    <x-input :md="$isVertical ? 3 : 12" :row="!$isVertical" type="select" name="is_selected" :errors="$errors" :label="__('validation.attributes.is_selected')"
        :value="isset($gallery) ? ($gallery['selected_at'] ? '1' : '') : ''" :options="['' => __('No'), '1' => __('Yes')]" />
    <x-input :md="$isVertical ? 3 : 12" :row="!$isVertical" name="gallery_order" :errors="$errors" :label="__('validation.attributes.gallery_order')"
        :value="isset($gallery) ? $gallery['gallery_order'] : ''" />
    <input type="hidden" name="gallery_category" value="{{ $gallery_category }}" />
    <input type="hidden" name="short_gallery_type" value="{{ $short_gallery_type }}" />
    <input type="hidden" name="gallery_id" value="{{ $gallery_id }}" />
    <x-button-submit :md="$isVertical ? 3 : 12" mt="2" name="submit" :row="!$isVertical" label="ㅤ"
        :class="isset($gallery) ? 'btn-primary' : 'btn-success'">
        {{ isset($gallery) ? __('Edit') : __('Upload') }}
    </x-button-submit>
    @if ($isVertical)
        </div>
    @endif
</x-form>
