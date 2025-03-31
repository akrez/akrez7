@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('Package')]))
@section('subheader', $package['guaranty'] . ' ' . $package['description'])

@section('content')
    @include('package._form', [
        'product' => $product,
        'package' => $package,
    ])
@endsection
