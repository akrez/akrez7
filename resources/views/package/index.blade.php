@extends('layouts.app')

@section('header', __('Packages'))
@section('subheader', $product['name'])

@section('content')
    <div class="row mb-2">
        @include('package._form', ['isVertical' => true])
    </div>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-hover table-bordered align-middle rounded-3 text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">@lang('validation.attributes.status')</th>
                        <th scope="col">@lang('validation.attributes.price')</th>
                        <th scope="col">@lang('validation.attributes.color_id')</th>
                        <th scope="col">@lang('validation.attributes.guaranty')</th>
                        <th scope="col">@lang('validation.attributes.description')</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody dir="ltr">
                    @forelse ($packages as $package)
                        <tr
                            class="{{ $package['package_status']['value'] === \App\Enums\PackageStatusEnum::DEACTIVE->value
                                ? 'table-danger'
                                : ($package['package_status']['value'] === \App\Enums\PackageStatusEnum::OUT_OF_STOCK->value
                                    ? 'table-warning'
                                    : 'table-success') }}">

                            <td>{{ $package['package_status'] ? $package['package_status']['trans'] : '' }}</td>

                            <td scope="col">{{ number_format($package['price']) }}</td>

                            <td scope="col">
                                @if ($package['color_id'] && \Arr::get($colorsIdArray, $package['color_id']))
                                    <span
                                        style="border: 1px solid black; background-color: {{ \Arr::get($colorsIdArray, $package['color_id'] . '.code') }};">
                                        ⠀⠀⠀
                                    </span>
                                    <span class="px-1"> {{ \Arr::get($colorsIdArray, $package['color_id'] . '.name') }} </span>
                                @endif
                            </td>
                            <td scope="col">{{ $package['guaranty'] }}</td>
                            <td scope="col">{{ $package['description'] }}</td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('products.packages.edit', ['product_id' => $product['id'], 'id' => $package['id']]) }}">
                                    @lang('Edit')
                                </a>
                            </td>
                            <td>
                                <form
                                    action="{{ route('products.packages.destroy', ['product_id' => $product['id'], 'id' => $package['id']]) }}"
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
