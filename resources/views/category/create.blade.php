@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('Category')]))

@section('content')
    @include('category._form')
@endsection
