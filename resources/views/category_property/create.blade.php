@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('Property')]))

@section('content')
    @include('category_property._form')
@endsection
