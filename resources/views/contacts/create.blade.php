@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('Contact')]))

@section('content')
    @include('contacts._form')
@endsection
