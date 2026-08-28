@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('BaleBot')]))

@section('content')
    @include('bale_bot._form')
@endsection
