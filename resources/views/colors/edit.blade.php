@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('Color')]))
@section('subheader', $color['name'])

@section('content')
    @include('colors._form', [
        'color' => $color,
    ])
@endsection
