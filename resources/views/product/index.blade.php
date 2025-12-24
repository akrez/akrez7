@extends('layouts.app')

@section('header', __('Products'))

@section('content')
    <div class="row pb-2">
        @include('product._form', ['isVertical' => true])
    </div>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle rounded-3 text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">@lang('product_images')</th>
                        <th scope="col">@lang('validation.attributes.code')</th>
                        <th scope="col">@lang('validation.attributes.name')</th>
                        <th scope="col">@lang('validation.attributes.status')</th>
                        <th scope="col">@lang('validation.attributes.created_at')</th>
                        <th scope="col">@lang('validation.attributes.updated_at')</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody dir="ltr">
                    @forelse ($products as $product)
                        <tr
                            class="{{ \Arr::get($product, 'product_status.value') === \App\Enums\ProductStatusEnum::DEACTIVE->value ? 'table-danger' : '' }}">
                            <td>
                                @foreach (\Arr::get($galleries, \App\Enums\GalleryCategoryEnum::PRODUCT_IMAGE->value, []) as $productImage)
                                    @if ($productImage['gallery_id'] == $product['id'])
                                        <a href="{{ $productImage['url'] }}" target="_blank">
                                            <img src="{{ $productImage['url'] }}" class="img-fluid max-height-38-px">
                                        </a>
                                    @endif
                                @endforeach
                            </td>
                            <td>{{ $product['code'] }}</td>
                            <td>{{ $product['name'] }}</td>
                            <td>{{ \Arr::get($product, 'product_status.trans') }}</td>
                            <td>{{ $product['created_at']['fa'] }}</td>
                            <td>{{ $product['updated_at']['fa'] }}</td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('products.product_properties.create', ['product_id' => $product['id']]) }}">
                                    @lang('Properties')
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('products.product_tags.create', ['product_id' => $product['id']]) }}">
                                    @lang('Tags')
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('products.packages.index', ['product_id' => $product['id']]) }}">
                                    @lang('Packages')
                                </a>
                            </td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('galleries.index', [
                                        'gallery_category' => \App\Enums\GalleryCategoryEnum::PRODUCT_IMAGE->value,
                                        'gallery_id' => $product['id'],
                                    ]) }}">
                                    @lang('product_images')
                                </a>
                            </td>
                            <td dir="rtl">
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('products.edit', ['id' => $product['id']]) }}">
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
