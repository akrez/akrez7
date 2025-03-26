@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('Product')]))
@section('subheader', $product['name'])

@section('content')
    @include('products._form', [
        'product' => $product,
    ])
@endsection
