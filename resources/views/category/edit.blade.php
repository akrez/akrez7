@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('Category')]))
@section('subheader', $category['name'])

@section('content')
    @include('category._form', [
        'category' => $category,
    ])
@endsection
