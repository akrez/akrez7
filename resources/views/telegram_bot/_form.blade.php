@php
    $isVertical = isset($isVertical) && $isVertical;
@endphp

<x-form :method="isset($telegramBot) ? 'PUT' : 'POST'" :action="isset($telegramBot)
    ? route('telegram_bots.update', ['id' => $telegramBot['id']])
    : route('telegram_bots.store')">
    @if ($isVertical)
        <div class="row">
    @endif
    <x-input :md="6" :row="!$isVertical" name="telegram_token" :errors="$errors" :value="isset($telegramBot) ? $telegramBot['telegram_token'] : ''" />
    @if ($isVertical)
        </div>
    @endif
    <x-button-submit :md="3" name="submit" :errors="$errors" :class="isset($telegramBot) ? 'btn-primary' : 'btn-success'">
        {{ isset($telegramBot) ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
