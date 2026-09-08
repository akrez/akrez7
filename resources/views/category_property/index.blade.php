@extends('layouts.app')

@section('header', __('Properties'))

@section('content')
    <div class="row mb-2">
        @include('category_property._form', ['isVertical' => true])
    </div>
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle rounded-3 text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">@lang('validation.attributes.category_id')</th>
                        <th scope="col">@lang('validation.attributes.property_key')</th>
                        <th scope="col">@lang('validation.attributes.filter_type')</th>
                        <th scope="col">@lang('validation.attributes.unit')</th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($category_properties as $category_property)
                        <tr>
                            <td>
                                @if ($category_property['category_id'])
                                    {{ collect($categories)->firstWhere('id', $category_property['category_id'])['name'] }}
                                @else
                                    <span class="badge bg-secondary">@lang('Global')</span>
                                @endif
                            </td>
                            <td>{{ $category_property['property_key'] }}</td>
                            <td>{{ \Arr::get($category_property, 'filter_type.trans') ?? '' }}</td>
                            <td>{{ $category_property['unit'] ?? '' }}</td>
                            <td>
                                <a class="btn btn-light border border-dark w-100"
                                    href="{{ route('category_properties.edit', ['id' => $category_property['id']]) }}">
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
