@extends('layouts.app')

@section('title', __('Verify Your Email Address'))

@section('content')
    <div class="row">
        @if (session('resent'))
            <div class="alert alert-success" role="alert">
                {{ __('A fresh verification link has been sent to your email address.') }}
            </div>
        @endif
        <div class="col-md-8 d-flex align-items-center">

            <div class="row mb-3">
                <div class="col-md-4 col-form-label text-md-end">
                </div>
                <div class="col-md-8">
                    <h1>{{ __('Verify Your Email Address') }}</h1>
                </div>

                <div class="col-md-4 col-form-label text-md-end">
                </div>
                <div class="col-md-8">
                    {{ __('Before proceeding, please check your email for a verification link.') }}
                    {{ __('If you did not receive the email') }},
                    <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                        @csrf
                        <button type="submit"
                            class="btn btn-link p-0 m-0 align-baseline">{{ __('click here to request another') }}</button>.
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <img src="{{ asset('images/story/verify.svg') }}" alt="">
        </div>
    </div>
@endsection
