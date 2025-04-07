@php
    $title = \Arr::get($data, 'blog.name');
    $shortDescription = \Arr::get($data, 'blog.short_description', '');
    $description = \Arr::get($data, 'blog.description', '');
    $titleShortDescription = $title . ($shortDescription ? ' | ' . $shortDescription : '');
    $tags = collect(Arr::get($data, 'products', []))->pluck('product_tags')->flatten()->unique()->sort()->toArray();
    $products = collect(Arr::get($data, 'products', []));
    $contacts = collect(Arr::get($data, 'contacts', []));
    $contactSize = $contacts->count() ? max(4, intval(12 / count($contacts))) : 4;
    $whmq = '__contain';
    $logoGallery = \Arr::get($data, 'blog.galleries.blog_logo.0');
    $logoUrl = $logoGallery ? $logoGallery['base_url'] . '/576__contain/' . $logoGallery['name'] : null;
    $heroUrl = url('images/hero.jpg');
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
        <link rel="stylesheet" href="{{ url('npm/bootstrap@5.3.3/dist/css/bootstrap.rtl.min.css') }}" />
        <link rel="stylesheet" href="{{ url('npm/vazir-font@30.1.0/dist/font-face.css') }}" />
        <link rel="stylesheet" href="{{ url('npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css') }}">
        <link rel="stylesheet" href="{{ url('css/blog.css') }}">

        <style>
            .bg {
                background-image: url("{{ url('images/bg.png') }}");
            }
        </style>

        @yield('POS_HEAD')
    </head>

    <body dir="rtl">
        @yield('POS_BEGIN')
        <div class="container-fluid">
            <div class="row align-items-center min-vh-100 bg">
                <div class="col-md-1">
                </div>
                <div class="col-md-3">
                    @if ($logoUrl)
                        <img class="w-100 rounded m-auto" alt="{{ $title }}" src="{{ $logoUrl }}">
                    @endif
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-12 d-flex justify-content-center">
                            <h1 class="me-2 text-shadow-white">{{ $title }}</h1>
                            <h2 class="h1 text-secondary text-shadow-white ">{{ $shortDescription }}</h2>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12 text-center">
                            <h4 class="text-justify text-shadow-white">{{ $description }}</h4>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="container">
            <div class="row py-3">
                <div class="col-12 text-center">
                    <button class="btn rounded-pill px-4 mb-2 btn-success" data-filter-tag="">
                        {{ 'همه محصولات ' . $title }}
                    </button>
                </div>
                <div class="col-12 text-center">
                    @foreach ($tags as $tagKey => $tag)
                        <button class="btn rounded-pill px-4 mb-1 btn-outline-success"
                            data-filter-tag="{{ md5($tag) }}">
                            {{ $tag }}
                        </button>
                    @endforeach
                </div>
            </div>

            <div class="row">
                <div class="col-sm-12 mb-4">
                    <div class="container-fluid">
                        <div class="row">
                            @foreach ($products as $productKey => $product)
                                <div class="thumbnail border pt-3 pb-3 col-sm-6 col-md-4 col-lg-2"
                                    data-filter-tags="{{ json_encode(array_map('md5', $product['product_tags'])) }}">
                                    @if (count($product['galleries']['product_image']) == 1)
                                        <img class="w-100 mb-3 rounded"
                                            src="{{ $product['galleries']['product_image'][0]['base_url'] . '/576__contain/' . $product['galleries']['product_image'][0]['name'] }}"
                                            alt="{{ $product['name'] }}">
                                    @elseif (count($product['galleries']['product_image']) > 1)
                                        <div id="product-carousel-{{ $productKey }}"
                                            class="carousel mb-3 carousel-dark slide">
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
                                    <div class="card-body">
                                        <h5 class="card-title font-weight-bold pb-2">{{ $product['name'] }}</h5>
                                        <p class="card-text">
                                            @foreach ($product['product_properties'] as $property)
                                                <strong>{{ $property['property_key'] }}:</strong>
                                                {{ implode(', ', $property['property_values']) }}<br>
                                            @endforeach
                                        </p>
                                    </div>
                                    @if ($product['packages'])
                                        <div class="card-footer text-body-secondary">
                                            @foreach ($product['packages'] as $package)
                                                <div class="mt-3 p-2 border small rounded">
                                                    <div class="d-flex">
                                                        <div class="package-width">🏷️</div>
                                                        <div>{{ number_format($package['price']) }} ﷼</div>
                                                    </div>
                                                    @if ($package['color'])
                                                        <div class="d-flex">
                                                            <div class="package-width"
                                                                style="color: {{ $package['color']['code'] }};">⦿</div>
                                                            <div>{{ $package['color']['name'] }}</div>
                                                        </div>
                                                    @endif
                                                    @if ($package['description'])
                                                        <div class="d-flex">
                                                            <div class="package-width">✔️</div>
                                                            <div>{{ $package['description'] }}</div>
                                                        </div>
                                                    @endif
                                                    @if ($package['guaranty'])
                                                        <div class="d-flex">
                                                            <div class="package-width">💯</div>
                                                            <div>{{ $package['guaranty'] }}</div>
                                                        </div>
                                                    @endif
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($contacts->count())
            <footer class="footer mt-auto py-3 bg-light">
                <div class="container">
                    <div class="row">
                        @foreach ($contacts as $contact)
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
                            <div class="col-lg-{{ $contactSize }} py-3">
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
        <script src="{{ asset('npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.js') }}"></script>
        <script>
            document.querySelectorAll("[data-filter-tag]").forEach(function(radioFilterElement) {
                radioFilterElement.onclick = function() {
                    tag = this.getAttribute('data-filter-tag');
                    selectedTagBtn = this;
                    document.querySelectorAll("[data-filter-tag]").forEach(tagBtn => {
                        if (tagBtn == selectedTagBtn) {
                            tagBtn.classList.remove('btn-outline-success');
                            tagBtn.classList.add('btn-success');
                        } else {
                            tagBtn.classList.add('btn-outline-success');
                            tagBtn.classList.remove('btn-success');
                        }
                    });
                    document.querySelectorAll("[data-filter-tags]").forEach(productElement => {
                        const hasTag = JSON.parse((productElement.getAttribute('data-filter-tags')))
                            .includes(tag);
                        productElement.style.display = (tag && !hasTag ? 'none' : 'block');
                    });
                }
            });
        </script>
        @yield('POS_END')
    </body>

    </html>
@endspaceless
