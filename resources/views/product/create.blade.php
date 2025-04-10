@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('Product')]))

@section('content')
    @include('product._form')
@endsection
