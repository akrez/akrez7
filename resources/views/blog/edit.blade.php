@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('Blog')]))
@section('subheader', $blog->name)

@section('content')
    @include('blog._form', [
        'blog' => $blog,
    ])
@endsection
