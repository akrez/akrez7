@extends('layouts.app')

@section('header', __('Create :name', ['name' => __('TelegramBot')]))

@section('content')
    @include('telegram_bot._form')
@endsection
