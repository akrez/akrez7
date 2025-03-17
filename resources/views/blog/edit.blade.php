@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('Blog')]))
@section('subheader', $blog->name)

@section('content')
    @include('blogs._form', [
        'blog' => $blog,
    ])
@endsection
