@php
    $isVertical = isset($isVertical) && $isVertical;
@endphp

<x-form :method="isset($baleBot) ? 'PUT' : 'POST'" :action="isset($baleBot)
    ? route('bale_bots.update', ['id' => $baleBot['id']])
    : route('bale_bots.store')">
    @if ($isVertical)
        <div class="row">
    @endif
    <x-input :md="3" :row="!$isVertical" name="bale_bot_status" :errors="$errors" :value="isset($baleBot) ? $baleBot['bale_bot_status']['value'] : ''"
        type="select" :label="__('validation.attributes.status')" :options="\App\Enums\BaleBotStatusEnum::toArray()" />
    <x-input :md="3" :row="!$isVertical" name="bale_token" :errors="$errors" :value="isset($baleBot) ? $baleBot['bale_token'] : ''" />
    <x-input :md="3" :row="!$isVertical" name="admin" :errors="$errors" :value="isset($baleBot) ? $baleBot['admin'] : ''" />
    <x-input :md="3" :row="!$isVertical" name="user_service" :errors="$errors" :value="isset($baleBot) ? $baleBot['user_service'] : ''" type="checkbox"
        :label="__('validation.attributes.user_service')" />
    <x-input :md="3" :row="!$isVertical" name="notify_admin_on_invoice" :errors="$errors" :value="isset($baleBot) ? $baleBot['notify_admin_on_invoice'] : ''" type="checkbox"
        :label="__('validation.attributes.notify_admin_on_invoice')" />
    @if ($isVertical)
        </div>
    @endif
    <x-button-submit :md="3" name="submit" :errors="$errors" :class="isset($baleBot) ? 'btn-primary' : 'btn-success'">
        {{ isset($baleBot) ? __('Edit') : __('Create') }}
    </x-button-submit>
</x-form>
