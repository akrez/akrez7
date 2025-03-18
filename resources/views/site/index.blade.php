@extends('layouts.app')

@section('content')
<div class="align-items-center rounded-3 border overflow-hidden pb-xl-5 pb-lg-5 pb-md-5 pt-5 mb-5 bg-body">
    <div class="row">
        <div class="col-lg-7 text-center">
            <h1 class="display-4 fw-bold lh-1">تجارت بدون مرز!</h1>
            <figure class="lead p-3 mb-0 rounded-3">
                <blockquote class="blockquote">
                    <p>
                        اگر کسب و کار شما اینترنتی نیست متاسفانه شما صاحب یک کسب و کار از رده خارج و رو به زوال هستید اگر کسب و کارتان در اینترنت نباشد به زودی از بازار هم حذف خواهید شد
                    </p>
                    <figcaption class="blockquote-footer pt-3">
                        <cite title="Source Title">بیل گیتس</cite>
                        بنیان گذار مایکروسافت
                    </figcaption>
                </blockquote>
            </figure>
        </div>
        <div class="col-lg-4 m-auto p-0">
            <img class="rounded-3 img-fluid" src="{{asset('images/story/dashboard.svg')}}">
        </div>
    </div>
</div>
@endsection