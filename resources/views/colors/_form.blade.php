<x-form :method="isset($color) ? 'PUT' : 'POST'" :action="isset($color) ? route('colors.update', ['id' => $color['id']]) : route('colors.store')">

    @php
    $colorCode = old('code') ?? (isset($color) ? $color['code'] : '');
    @endphp

    <x-input name="code" :errors="$errors" :value="$colorCode" data-jscolor="{{ json_encode([
        'value'=> $colorCode, 
        'paletteCols' => 10, 
        'palette' => collect(__('colors'))->keys()->implode(' '),
    ]) }}" />

    <x-input name="name" :errors="$errors" :value="isset($color) ? $color['name'] : ''" />

    <x-button-submit name="submit" :errors="$errors" :class="isset($color) ? 'btn-primary' : 'btn-success'">
        {{ isset($color) ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
