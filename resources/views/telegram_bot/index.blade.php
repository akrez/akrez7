@extends('layouts.app')

@section('header', __('TelegramBots'))

@section('content')
    <div class="row mb-2">
        @include('telegram_bot._form', ['isVertical' => true])
    </div>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle rounded-3 text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">@lang('validation.attributes.status')</th>
                        <th scope="col">@lang('validation.attributes.telegram_token')</th>
                        <th scope="col">@lang('Upload :name Attribute', ['name' => __('TelegramBot')])</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($telegramBots as $telegramBot)
                        <tr
                            class="{{ $telegramBot['telegram_bot_status']['value'] === \App\Enums\TelegramBotStatusEnum::DEACTIVE->value
                                ? 'table-danger'
                                : 'table-success' }}">

                            <td>{{ $telegramBot['telegram_bot_status'] ? $telegramBot['telegram_bot_status']['trans'] : '' }}
                            </td>
                            <td class="font-monospace" dir="ltr">
                                {{ Str::mask($telegramBot['telegram_token'], '*', 14, 22) }}
                            </td>
                            <td>
                                <x-form action="{{ route('telegram_bots.upload', ['id' => $telegramBot['id']]) }}"
                                    method="POST">
                                    <div class="btn-group w-100">
                                        <label for="btn-name-{{ $telegramBot['id'] }}"
                                            class="btn btn-primary border border-dark">
                                            @lang('validation.attributes.name')
                                        </label>
                                        <label for="btn-short_description-{{ $telegramBot['id'] }}"
                                            class="btn btn-primary border border-dark">
                                            @lang('validation.attributes.short_description')
                                        </label>
                                        <label for="btn-description-{{ $telegramBot['id'] }}"
                                            class="btn btn-primary border border-dark">
                                            @lang('validation.attributes.description')
                                        </label>
                                    </div>

                                    <input id="btn-name-{{ $telegramBot['id'] }}" type="submit" name="attribute_name"
                                        value="name" class="d-none">
                                    <input id="btn-short_description-{{ $telegramBot['id'] }}" type="submit"
                                        name="attribute_name" value="short_description" class="d-none">
                                    <input id="btn-description-{{ $telegramBot['id'] }}" type="submit"
                                        name="attribute_name" value="description" class="d-none">
                                </x-form>
                            </td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('telegram_bots.edit', ['id' => $telegramBot['id']]) }}">
                                    @lang('Edit')
                                </a>
                            </td>
                            <td>
                                <x-form action="{{ route('telegram_bots.destroy', ['id' => $telegramBot['id']]) }}"
                                    method="DELETE">
                                    <button class="btn btn-danger border border-dark w-100">
                                        @lang('Delete')
                                    </button>
                                </x-form>
                            </td>
                        </tr>
                    @empty
                        <tr class="table-warning">
                            <td colspan="99">
                                @lang('Not Found')
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
