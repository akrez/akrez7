@extends('layouts.app')

@section('header', __('Colors'))

@section('content')
    <div class="row mb-2">
        @include('color._form', ['isVertical' => true])
    </div>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle rounded-3 text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">@lang('validation.attributes.code')</th>
                        <th scope="col">@lang('validation.attributes.name')</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody dir="ltr">
                    @forelse ($colors as $color)
                        <tr dir="ltr">
                            <td scope="col">
                                <span style="border: 1px solid black; background-color: {{ $color['code'] }};">⠀⠀⠀</span>
                                <code> {{ $color['code'] }} </code>
                            </td>
                            <td scope="col">{{ $color['name'] }}</td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('colors.edit', ['id' => $color['id']]) }}">
                                    @lang('Edit')
                                </a>
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
