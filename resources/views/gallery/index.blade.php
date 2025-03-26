@extends('layouts.app')

@section('header', __('enums.' . $gallery_category))
@section('subheader', app('ActiveBlog')->name())

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="col-12 mb-4">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column justify-content-end">
                        @include('gallery._form')
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            @if ($galleries)
                <div class="row">
                    @foreach ($galleries as $gallery)
                        <div class="col-4 mb-4">
                            <div class="card h-100">
                                <img src="{{ \Arr::get($gallery, 'url') }}" class="card-img-top">
                                <div class="card-body d-flex flex-column justify-content-end">
                                    <div class="card-text">
                                        <div>
                                            <strong>@lang('validation.attributes.is_selected'): </strong>
                                            {{ $gallery['selected_at'] ? __('Yes') : __('No') }}
                                        </div>
                                        <div>
                                            <strong>@lang('validation.attributes.gallery_order'): </strong>
                                            {{ $gallery['gallery_order'] }}
                                        </div>
                                    </div>
                                    <a href="{{ route('galleries.edit', ['id' => $gallery['id']]) }}"
                                        class="btn btn-default border border-dark w-100 mt-4">
                                        @lang('Edit')
                                    </a>
                                    <x-form :action="route('galleries.destroy', ['id' => $gallery['id']])" method="DELETE">
                                        <button class="btn btn-danger border border-dark w-100 mt-4">
                                            @lang('Delete')
                                        </button>
                                    </x-form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="row">
                    <div class="col-md-12">
                        <div class="alert alert-warning" role="alert">
                            @lang('Not Found')
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
    </div>
@endsection
