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
                        <th scope="col">@lang('validation.attributes.telegram_token')</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody dir="ltr">
                    @forelse ($telegramBots as $telegramBot)
                        <tr dir="ltr">
                            <td class="font-monospace" dir="ltr">{{ Str::mask($telegramBot['telegram_token'], '*', 14, 22) }}</td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('telegram_bots.edit', ['id' => $telegramBot['id']]) }}">
                                    @lang('Edit')
                                </a>
                            </td>
                            <td>
                                <form
                                    action="{{ route('telegram_bots.destroy', ['id' => $telegramBot['id']]) }}"
                                    method="post">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-danger border border-dark w-100">
                                        @lang('Delete')
                                    </button>
                                </form>
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
