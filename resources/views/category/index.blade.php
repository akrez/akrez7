@extends('layouts.app')

@section('header', __('Categories'))

@section('content')
    <div class="row mb-2">
        @include('category._form', ['isVertical' => true])
    </div>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle rounded-3 text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">@lang('validation.attributes.name')</th>
                        <th scope="col">@lang('validation.attributes.status')</th>
                        <th scope="col">@lang('validation.attributes.category_order')</th>
                        <th scope="col">@lang('validation.attributes.created_at')</th>
                        <th scope="col">@lang('validation.attributes.updated_at')</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($categories as $category)
                        <tr class="{{ \Arr::get($category, 'category_status.value') === \App\Enums\CategoryStatusEnum::DEACTIVE->value ? 'table-danger' : '' }}">
                            <td>{{ $category['name'] }}</td>
                            <td>{{ \Arr::get($category, 'category_status.trans') }}</td>
                            <td>{{ $category['category_order'] }}</td>
                            <td>{{ $category['created_at']['fa'] }}</td>
                            <td>{{ $category['updated_at']['fa'] }}</td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('categories.edit', ['id' => $category['id']]) }}">
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
