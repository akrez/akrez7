@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('Package')]))
@section('subheader', $product['name'])

@section('content')
    @include('product.package._form', [
        'product' => $product,
        'package' => $package,
    ])
@endsection
