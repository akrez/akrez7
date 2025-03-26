@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('blog_logo')]))
@section('subheader', app('ActiveBlog')->name())

@section('content')
    <div class="row">
        <div class="col-md-3">
            <div class="col-12 mb-4">
                <div class="card h-100">
                    <img src="{{ \Arr::get($gallery, 'url') }}" class="card-img-top">
                    <div class="card-body d-flex flex-column justify-content-end">
                        @include('gallery._form', [
                            'gallery' => $gallery,
                            'gallery_category' => $gallery['gallery_category']['value'],
                            'short_gallery_type' => $gallery['short_gallery_type'],
                            'gallery_id' => $gallery['gallery_id'],
                        ])
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
