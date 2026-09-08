@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('Property')]))
@section('subheader', $category_property['property_key'])

@section('content')
    @include('category_property._form', [
        'category_property' => $category_property,
    ])
@endsection
