@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('Package')]))

@section('content')
    @include('product.package._form', [
        'product' => $product,
    ])
@endsection
