@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('BaleBot')]))
@section('subheader', __('BaleBot'))

@section('content')
    @include('bale_bot._form', [
        'baleBot' => $baleBot,
    ])
@endsection
