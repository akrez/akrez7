@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('TelegramBot')]))
@section('subheader', __('TelegramBot'))

@section('content')
    @include('telegram_bot._form', [
        'telegramBot' => $telegramBot,
    ])
@endsection
