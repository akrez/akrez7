@extends('layouts.app')

@section('header', __('Properties'))
@section('subheader', $product['name'])

@section('content')
    @include('product_property._form', [
        'product' => $product,
        'productPropertiesText' => $productPropertiesText,
    ])
@endsection
