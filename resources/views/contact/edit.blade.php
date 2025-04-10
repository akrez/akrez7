@extends('layouts.app')

@section('header', __('Edit :name', ['name' => __('Contact')]))
@section('subheader', $contact['contact_key'])

@section('content')
    @include('contact._form', [
        'contact' => $contact,
    ])
@endsection
