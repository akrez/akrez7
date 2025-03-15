@extends('layouts.app')

@section('header', __('Blogs'))

@section('content')
    <div class="row mb-2">
        <div class="col-md-2 pull-right">
            <a class="btn btn-success w-100" href="{{ route('blogs.create') }}">@lang('Create :name', ['name' => __('Blog')])</a>
        </div>
    </div>
    <div class="row">
        @forelse ($blogs as $blog)
            <div class="col-md-3 my-3">
                <div class="card h-100">
                    <div class="card-body d-flex flex-column">
                        <h5 class="m-0">
                            <span
                                class="{{ $blog->blog_status == App\Enums\BlogStatus::ACTIVE ? 'text-success' : 'text-danger' }}">
                                {{ $blog->name }}
                            </span>
                            <small class="text-muted">{{ $blog->short_description }}</small>
                        </h5>
                        <p class="card-text text-justify py-2 mb-auto">{{ $blog->description }}</p>
                        <div class="row gy-1">
                            <div class="col-md-6">
                                <a href="{{ route('blogs.edit', ['id' => $blog->id]) }}"
                                    class="btn btn-primary w-100">@lang('Edit')</a>
                            </div>
                            <div class="col-md-6">
                                <form enctype="multipart/form-data" method="post"
                                    action="{{ route('blogs.active', ['id' => $blog->id]) }}">
                                    @csrf
                                    @method('PATCH')
                                    <input type="submit" class="btn btn-primary w-100" value="@lang('Select')">
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-md-12">
                <div class="alert alert-warning" role="alert">
                    @lang('Not Found')
                </div>
            </div>
        @endforelse
    </div>
@endsection
