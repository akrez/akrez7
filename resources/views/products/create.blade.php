@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('Product')]))

@section('content')
    @include('products._form')
@endsection
