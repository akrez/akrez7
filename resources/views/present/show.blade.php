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

            .category-menu {
                border-bottom: 3px solid transparent;
                font-size: var(--bs-btn-font-size);
            }

            .category-menu-active,
            .category-menu:hover {
                border-bottom: 3px solid rgb(220, 53, 69);
            }

            .max-height-40px {
                max-height: 40px
            }
        </style>

        @yield('POS_HEAD')
    </head>

    <body dir="rtl" class="bg">
        @yield('POS_BEGIN')

        <nav class="navbar navbar-expand-sm p-0 py-2 bg-body-tertiary sticky-top border-bottom">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="d-flex align-items-center flex-grow-1">
                        @if ($logoUrl)
                            <img class="img-fluid p-0 ps-2 m-0 max-height-40px" alt="{{ $title }}"
                                src="{{ $logoUrl }}">
                        @endif
                        <h1 class="h4 text-danger p-0 ps-2 m-0 flex-shrink-0">{{ $title }}</h1>
                        <h2 class="h5 text-danger p-0 px-2 m-0 flex-shrink-0 m-0 d-none d-md-inline">{{ $shortDescription }}
                        </h2>
                        <input class="form-control p-2 m-0 mx-2" type="text">
                    </div>

                    <button class="btn btn-outline-danger flex-shrink-0 p-2" href="#invoice-form" data-bs-toggle="modal"
                        data-bs-target="#invoice-modal">
                        <span class="d-none d-md-inline pe-1">
                            ثبت پیش فاکتور
                        </span>
                        <i class="bi bi-cart mx-1"></i>
                    </button>
                </div>
            </div>
        </nav>

        <nav class="navbar navbar-expand-sm py-2 p-md-0 bg-body-tertiary">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                            aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                            دسته‌بندی کالاها
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <nav class="nav nav-underline gap-0">
                                <button class="btn p-2 m-0 me-2 rounded-0 category-menu fw-bold" data-filter-tag="">
                                    {{ 'همه محصولات ' . $title }}
                                </button>
                                @foreach ($tags as $tagKey => $tag)
                                    <button class="btn p-2 m-0 me-2 rounded-0 category-menu"
                                        data-filter-tag="{{ md5($tag) }}">{{ $tag }}</button>
                                @endforeach
                            </nav>
                        </div>
                    </div>
                </div>
            </div>
        </nav>

        <div class="container">
            <div class="row mt-3 row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6 mb-3 g-0">
                @foreach ($products as $productKey => $product)
                    <div class="col" data-filter-tags="{{ json_encode(array_map('md5', $product['product_tags'])) }}">
                        <div class="card rounded-0 h-100">

                            @if (count($product['galleries']['product_image']) > 0)
                                <div class="p-3">
                                    @if (count($product['galleries']['product_image']) == 1)
                                        <img class="w-100 rounded"
                                            src="{{ $product['galleries']['product_image'][0]['base_url'] . '/576__contain/' . $product['galleries']['product_image'][0]['name'] }}"
                                            alt="{{ $product['name'] }}">
                                    @elseif (count($product['galleries']['product_image']) > 1)
                                        <div id="product-carousel-{{ $productKey }}"
                                            class="carousel carousel-dark slide">
                                            <div class="carousel-inner">
                                                @foreach ($product['galleries']['product_image'] as $productImage)
                                                    <div
                                                        class="carousel-item @if ($loop->first) active @endif">
                                                        <img class="w-100 rounded"
                                                            src="{{ $productImage['base_url'] . '/576__contain/' . $productImage['name'] }}"
                                                            alt="{{ $product['name'] }}">
                                                    </div>
                                                @endforeach
                                            </div>
                                            <button class="carousel-control-prev" type="button"
                                                data-bs-target="#product-carousel-{{ $productKey }}"
                                                data-bs-slide="prev">
                                                <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Previous</span>
                                            </button>
                                            <button class="carousel-control-next" type="button"
                                                data-bs-target="#product-carousel-{{ $productKey }}"
                                                data-bs-slide="next">
                                                <span class="carousel-control-next-icon" aria-hidden="true"></span>
                                                <span class="visually-hidden">Next</span>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            @endif

                            <div class="card-body">
                                <h5 class="h6 card-title font-weight-bold">{{ $product['name'] }}</h5>
                                <div class="card-text">
                                    @foreach ($product['product_properties'] as $property)
                                        <div>
                                            <strong>{{ $property['property_key'] }}</strong>
                                            {{ implode(', ', $property['property_values']) }}
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            @if ($product['packages'])
                                @foreach ($product['packages'] as $package)
                                    <div class="card-footer text-body-secondary d-flex flex-column">
                                        @if ($package['show_price'])
                                            <div>
                                                <b class="d-inline-block">
                                                    {{ number_format($package['price']) }}
                                                </b>
                                                <span class="ms-1 d-inline-block">﷼</span>
                                            </div>
                                        @endif
                                        @if ($package['guaranty'])
                                            <div class="pt-1">
                                                <b class="d-inline-block">گارانتی</b>
                                                <span class="ms-1 d-inline-block">{{ $package['guaranty'] }}</span>
                                            </div>
                                        @endif
                                        @if ($package['color'])
                                            <div class="pt-1">
                                                <span class="d-inline-block rounded"
                                                    style="border: 1px black solid; background-color: {{ $package['color']['code'] }};">⠀⠀⠀</span><span
                                                    class="d-inline-block ms-1">{{ $package['color']['name'] }}</span>
                                            </div>
                                        @endif
                                        @if ($package['description'])
                                            <div class="pt-1 d-inline-block">{{ $package['description'] }}</div>
                                        @endif

                                        @if ($package['package_status']['value'] === 'active')
                                            <div class="pt-1 input-group input-group-sm">
                                                <button
                                                    class="col-3 btn btn-light text-center border border-secondary-subtle plus-btn"
                                                    type="button">➕</button>
                                                <input class="col-6 form-control text-center input-spin-none"
                                                    type="number"
                                                    value="{{ old('invoice_items[' . $package['id'] . ']cnt', 0) }}"
                                                    name="invoice_items[{{ $package['id'] }}][cnt]" form="invoice-form">
                                                @if ($package['unit'])
                                                    <span class="input-group-text">{{ $package['unit'] }}</span>
                                                @endif
                                                <button
                                                    class="col-3 btn btn-light text-center border border-secondary-subtle minus-btn"
                                                    type="button">➖</button>
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="modal" id="invoice-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">ثبت پیش فاکتور</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <x-form method="POST" action="{{ $storeInvoiceAction }}" id="invoice-form">
                            <x-input :label="__('validation.attributes.invoice_delivery.name')" :md="12" name="invoice_delivery[name]" :errors="$errors"
                                :value="isset($invoice_delivery['name']) ? $invoice_delivery['name'] : ''" :mt="0" />
                            <x-input :label="__('validation.attributes.invoice_delivery.mobile')" :md="12" name="invoice_delivery[mobile]" :errors="$errors"
                                :value="isset($invoice_delivery['mobile']) ? $invoice_delivery['mobile'] : ''" />
                            <x-input :label="__('validation.attributes.invoice_delivery.city')" :md="12" name="invoice_delivery[city]" :errors="$errors"
                                :value="isset($invoice_delivery['city']) ? $invoice_delivery['city'] : ''" />
                            <x-input type="textarea" rows="2" :label="__('validation.attributes.invoice_delivery.address')" :md="12"
                                name="invoice_delivery[address]" :errors="$errors" :value="isset($invoice_delivery['address']) ? $invoice_delivery['address'] : ''" />
                            <x-input type="textarea" rows="2" :label="__('validation.attributes.invoice.invoice_description')" :md="12"
                                name="invoice[invoice_description]" :errors="$errors" :value="isset($invoice['invoice_description'])
                                    ? $invoice['invoice_description']
                                    : ''" />
                            <x-button-submit :md="8" name="submit" :errors="$errors"
                                :class="'btn-success'">ثبت</x-button-submit>
                        </x-form>
                    </div>
                </div>
            </div>
        </div>

        @if ($presenterContacts->count())
            <footer class="footer mt-auto py-3 bg-light">
                <div class="container-fluid">
                    <div class="row">
                        @foreach ($presenterContacts->toArray() as $contact)
                            @php
                                if ('address' == $contact['contact_type']['value']) {
                                    $icon = 'bi bi-geo-alt';
                                } elseif ('telegram' == $contact['contact_type']['value']) {
                                    $icon = 'bi bi-telegram';
                                } elseif ('whatsapp' == $contact['contact_type']['value']) {
                                    $icon = 'bi bi-whatsapp';
                                } elseif ('email' == $contact['contact_type']['value']) {
                                    $icon = 'bi bi-envelope';
                                } elseif ('instagram' == $contact['contact_type']['value']) {
                                    $icon = 'bi bi-instagram';
                                } else {
                                    $icon = 'bi bi-telephone';
                                }
                            @endphp
                            <div class="col-lg-{{ $presenterContactSize }} py-3">
                                <div class="info-item text-center">
                                    <div class="contact d-inline-block text-center">
                                        <div class="d-flex justify-content-center">
                                            <i class="{{ $icon }} fs-3em"></i>
                                        </div>
                                        <h3>{{ $contact['contact_key'] }}</h3>
                                        @if ($contact['contact_link'])
                                            <a class="h4 text-success text-decoration-none"
                                                href="{{ $contact['contact_link'] }}"
                                                dir="ltr">{{ $contact['contact_value'] }}</a>
                                        @else
                                            <div class="h4 text-secondary text-decoration-none" dir="ltr">
                                                {{ $contact['contact_value'] }}</div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </footer>
        @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                var plusButtons = document.querySelectorAll('.plus-btn');
                plusButtons.forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        var input = this.closest('div').querySelector('input');
                        var value = parseInt(input.value, 10) || 0;
                        input.value = value + 1;
                    });
                });

                var minusButtons = document.querySelectorAll('.minus-btn');
                minusButtons.forEach(function(button) {
                    button.addEventListener('click', function(e) {
                        var input = this.closest('div').querySelector('input');
                        var value = parseInt(input.value, 10) || 0;
                        input.value = (value > 1 ? value - 1 : 0);
                    });
                });
            });

            document.querySelectorAll("[data-filter-tag]").forEach(function(radioFilterElement) {
                radioFilterElement.onclick = function() {
                    tag = this.getAttribute('data-filter-tag');
                    selectedTagBtn = this;
                    document.querySelectorAll("[data-filter-tag]").forEach(tagBtn => {
                        if (tagBtn == selectedTagBtn) {
                            tagBtn.classList.add('category-menu-active');
                        } else {
                            tagBtn.classList.remove('category-menu-active');
                        }
                    });
                    document.querySelectorAll("[data-filter-tags]").forEach(productElement => {
                        const hasTag = JSON.parse((productElement.getAttribute('data-filter-tags')))
                            .includes(tag);
                        productElement.style.display = (tag && !hasTag ? 'none' : 'block');
                    });
                }
            });

            document.querySelector('form#invoice-form').addEventListener('submit', function(event) {

                event.preventDefault();

                $requiredAttributesMessage = {
                    'invoice_delivery[name]': 'فیلد نام الزامی است.',
                    'invoice_delivery[mobile]': 'فیلد شماره همراه الزامی است.',
                    'invoice_delivery[city]': 'فیلد شهر الزامی است.',
                    'invoice_delivery[address]': 'فیلد نشانی الزامی است.',
                };

                const oldFormData = new FormData(this);
                const newFormData = new FormData();

                let hasInvoiceItems = false;
                let validationErrors = [];

                for (let [key, value] of oldFormData.entries()) {
                    const nameParts = key.match(/invoice_items\[(\d+)\]\[cnt\]/);
                    if (nameParts) {
                        const packageId = nameParts[1];
                        const cnt = parseInt(value, 10);
                        if (cnt > 0) {
                            newFormData.append(`invoice_items[${packageId}][package_id]`, packageId);
                            newFormData.append(`invoice_items[${packageId}][cnt]`, cnt);
                            hasInvoiceItems = true;
                        }
                    } else {
                        if ((key in $requiredAttributesMessage) && value.trim() === '') {
                            validationErrors.push($requiredAttributesMessage[key]);
                        } else {
                            newFormData.append(key, value);
                        }
                    }
                }

                if (!hasInvoiceItems) {
                    validationErrors.push('سبد خرید شما خالی است.');
                }

                if (validationErrors.length > 0) {
                    Swal.fire({
                        icon: 'warning',
                        html: validationErrors.join('<br>'),
                        confirmButtonText: 'بستن'
                    });
                    return;
                }

                fetch(this.action, {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                        },
                        body: newFormData,
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 422) {
                            let errorMessage = '';
                            for (const key in data.errors) {
                                errorMessage += data.errors[key].join('<br>') + '<br>';
                            }
                            Swal.fire({
                                icon: 'warning',
                                html: errorMessage,
                                confirmButtonText: 'بستن'
                            });
                        } else if (data.status === 200 || data.status === 201) {
                            Swal.fire({
                                icon: 'success',
                                title: data.message,
                                confirmButtonText: 'بستن'
                            }).then(() => {
                                location.reload();
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: data.message,
                                confirmButtonText: 'بستن'
                            });
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            icon: 'error',
                            title: 'خطا',
                            confirmButtonText: 'بستن'
                        });
                    });
            });
        </script>
        @yield('POS_END')
    </body>

    </html>
@endspaceless
