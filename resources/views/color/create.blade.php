@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('Color')]))

@section('content')
    @include('color._form')
@endsection
