@php
    $title = \Arr::get($data, 'blog.name');
    $shortDescription = \Arr::get($data, 'blog.short_description', '');
    $description = \Arr::get($data, 'blog.description', '');
    $titleShortDescription = $title . ($shortDescription ? ' | ' . $shortDescription : '');
    $tags = collect(Arr::get($data, 'products', []))->pluck('product_tags')->flatten()->unique()->sort()->toArray();
    $products = collect(Arr::get($data, 'products', []));
    $contacts = collect(Arr::get($data, 'contacts', []));
    $whmq = '__contain';
    $logoGallery = \Arr::get($data, 'blog.galleries.blog_logo.0');
    $logoUrl = $logoGallery ? $logoGallery['base_url'] . '/576__contain/' . $logoGallery['name'] : null;
    $heroUrl = url('images/hero.jpg');
    $presenterContacts = collect($contacts)->filter(function ($contact, int $key) {
        return $contact['presenter_visible'];
    });
    $presenterContactSize = $presenterContacts->count() ? max(4, intval(12 / $presenterContacts->count())) : 4;
    $invoiceContacts = collect($contacts)->filter(function ($contact, int $key) {
        return $contact['invoice_visible'];
    });
@endphp

@spaceless
    <!doctype html>
    <html class="h-100" lang="{{ str_replace('_', '-', app()->getLocale()) }}">

    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <!-- CSRF Token -->
        <meta name="csrf-token" content="{{ csrf_token() }}">

        @if ($logoUrl)
            <link rel="shortcut icon" href="{{ $logoUrl }}">
        @endif

        <title>{{ $titleShortDescription }}</title>
        <meta name="description" content="{{ $description }}">

        <!-- CSS files -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <link rel="stylesheet" href="{{ url('assets/bootstrap-icons/bootstrap-icons.min.css') }}">
        <link rel="stylesheet" href="{{ url('assets/vazir-font/font-face.css') }}">
        <link rel="stylesheet" href="{{ url('css/blog.css') }}">

        <style>
            .bg {
                background-image: url("{{ url('images/bg.png') }}");
            }

            input.input-spin-none[type="number"]::-webkit-inner-spin-button,
            input.input-spin-none[type="number"]::-webkit-outer-spin-button {
                -webkit-appearance: none;
                margin: 0;
            }

            input.input-spin-none[type="number"] {
                -moz-appearance: textfield;
            }

            table .td-fit {
                width: 1%;
                white-space: nowrap;
            }
        </style>

        @yield('POS_HEAD')
    </head>

    <body dir="rtl">
        @yield('POS_BEGIN')
        <div class="container-fluid">
            <div class="row">
                <div class="col-12 mb-3">
                    <table class="table table-bordered m-0 w-100 my-3">
                        <tbody>
                            <tr>
                                <td class="text-center table-light td-fit" rowspan="2" style="width: 80px;">
                                    @if ($logoUrl)
                                        <img style="width: 64px; height: 64px;" alt="{{ $title }}"
                                            src="{{ $logoUrl }}">
                                    @endif
                                </td>
                                <td colspan="{{ $invoiceContacts->count() * 2 + 2 }}">
                                    {{ $title }}
                                </td>
                            </tr>
                            <tr>
                                @forelse ($invoiceContacts as $invoiceContact)
                                    <td class="table-light td-fit">{{ Arr::get($invoiceContact, 'contact_key') }}</td>
                                    <td>{{ Arr::get($invoiceContact, 'contact_value') }}</td>
                                @empty
                                @endforelse
                                <td class="table-light td-fit">@lang(':name id', ['name' => __('Invoice')])</td>
                                <td class="">
                                    {{ Arr::get($invoice, 'invoice_uuid') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    @include('invoice.__tables', ['invoice' => $invoice])
                </div>
            </div>
        </div>
        @yield('POS_END')
    </body>

    </html>
@endspaceless
