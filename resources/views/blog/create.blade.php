@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('Blog')]))

@section('content')
    @include('blog._form')
@endsection
