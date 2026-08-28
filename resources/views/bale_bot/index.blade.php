@extends('layouts.app')

@section('header', __('BaleBots'))

@section('content')
    <div class="row mb-2">
        @include('bale_bot._form', ['isVertical' => true])
    </div>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle rounded-3 text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">@lang('validation.attributes.status')</th>
                        <th scope="col">@lang('validation.attributes.bale_token')</th>
                        <th scope="col">@lang('validation.attributes.user_service')</th>
                        <th scope="col">@lang('validation.attributes.notify_admin_on_invoice')</th>
                        <th scope="col">@lang('validation.attributes.admin')</th>
                        <th scope="col">@lang('Upload :name Attribute', ['name' => __('BaleBot')])</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($baleBots as $baleBot)
                        <tr
                            class="{{ $baleBot['bale_bot_status']['value'] === \App\Enums\BaleBotStatusEnum::DEACTIVE->value
                                ? 'table-danger'
                                : 'table-success' }}">

                            <td>{{ $baleBot['bale_bot_status'] ? $baleBot['bale_bot_status']['trans'] : '' }}
                            </td>
                            <td class="font-monospace" dir="ltr">
                                {{ Str::mask($baleBot['bale_token'], '*', 14, 22) }}
                            </td>
                            <td>{{ $baleBot['user_service'] ? '✔️' : '❌' }}</td>
                            <td>{{ $baleBot['notify_admin_on_invoice'] ? '✔️' : '❌' }}</td>
                            <td class="font-monospace" dir="ltr">{{ $baleBot['admin'] }}</td>
                            <td>
                                <x-form action="{{ route('bale_bots.upload', ['id' => $baleBot['id']]) }}"
                                    method="POST">
                                    <div class="btn-group w-100">
                                        <label for="bale-btn-name-{{ $baleBot['id'] }}"
                                            class="btn btn-primary border border-dark">
                                            @lang('validation.attributes.name')
                                        </label>
                                        <label for="bale-btn-short_description-{{ $baleBot['id'] }}"
                                            class="btn btn-primary border border-dark">
                                            @lang('validation.attributes.short_description')
                                        </label>
                                        <label for="bale-btn-description-{{ $baleBot['id'] }}"
                                            class="btn btn-primary border border-dark">
                                            @lang('validation.attributes.description')
                                        </label>
                                    </div>

                                    <input id="bale-btn-name-{{ $baleBot['id'] }}" type="submit" name="attribute_name"
                                        value="name" class="d-none">
                                    <input id="bale-btn-short_description-{{ $baleBot['id'] }}" type="submit"
                                        name="attribute_name" value="short_description" class="d-none">
                                    <input id="bale-btn-description-{{ $baleBot['id'] }}" type="submit"
                                        name="attribute_name" value="description" class="d-none">
                                </x-form>
                            </td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('bale_bots.edit', ['id' => $baleBot['id']]) }}">
                                    @lang('Edit')
                                </a>
                            </td>
                            <td>
                                <x-form action="{{ route('bale_bots.destroy', ['id' => $baleBot['id']]) }}"
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
