@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('Color')]))

@section('content')
    @include('colors._form')
@endsection
