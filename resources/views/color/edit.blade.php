@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('Color')]))
@section('subheader', $color['name'])

@section('content')
    @include('color._form', [
        'color' => $color,
    ])
@endsection
