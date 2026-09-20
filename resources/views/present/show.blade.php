@php
    $title = \Arr::get($data, 'blog.name');
    $shortDescription = \Arr::get($data, 'blog.short_description', '');
    $description = \Arr::get($data, 'blog.description', '');
    $titleShortDescription = $title . ($shortDescription ? ' | ' . $shortDescription : '');
    $products = collect(Arr::get($data, 'products', []));
    $contacts = collect(Arr::get($data, 'contacts', []));
    $categories = collect(Arr::get($data, 'categories', []));
    $categoryProperties = collect(Arr::get($data, 'category_properties', []));
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

    $filterProducts = $products
        ->map(function ($product) {
            $properties = [];
            foreach ($product['product_properties'] as $productProperty) {
                $properties[$productProperty['property_key']] = $productProperty['property_values'];
            }

            return [
                'id' => $product['id'],
                'name' => $product['name'],
                'category' => strval($product['category_id']),
                'properties' => $properties,
            ];
        })
        ->values();

    $packageIds = $products
        ->flatMap(function ($product) {
            return collect($product['packages'])
                ->filter(function ($package) {
                    return $package['package_status']['value'] === 'active';
                })
                ->pluck('id');
        })
        ->values();

    $oldBasket = collect(old('invoice_items', []))
        ->mapWithKeys(function ($item, $key) {
            return [$key => (int) ($item['cnt'] ?? 0)];
        })
        ->all();
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

            .wh-48 {
                width: 48px;
                height: 48px;
            }

            .fs-7 {
                font-size: 0.875em !important;
            }

            .fs-8 {
                font-size: 0.75em !important;
            }

            [x-cloak] {
                display: none !important;
            }

            .basket-translate {
                transform: translate(-50%, -25%) !important;
            }
        </style>

        @yield('POS_HEAD')
    </head>

    <body dir="rtl" class="d-flex flex-column justify-content-between min-vh-100" x-data="presentPage({
        categories: @js($categories),
        properties: @js($categoryProperties),
        products: @js($filterProducts),
        packages: @js($packageIds),
        oldBasket: @js($oldBasket)
    })">
        @yield('POS_BEGIN')

        <div class="container-fluid">
            <div class="row sticky-top bg-white border border-light-subtle border-top-0 shadow">
                <div class="col-12 d-flex w-100 justify-content-between flex-wrap gap-3 py-2">
                    <div class="d-flex align-items-center text-center order-1">
                        @if ($logoUrl)
                            <img class="wh-48" alt="{{ $title }}" src="{{ $logoUrl }}">
                        @endif
                    </div>
                    <div class="d-flex flex-column text-center text-danger gap-2 order-2">
                        <h1 class="h5 m-0 text-nowrap fs-6 fw-bold">{{ $title }}</h1>
                        <h2 class="h6 m-0 text-nowrap fs-6">{{ $shortDescription }}</h2>
                    </div>
                    <div class="flex-grow-1 order-5 order-md-3 w-md-auto">
                        <input type="text" class="form-control form-control-lg rounded-pill bg-body-tertiary fs-7"
                            x-model.debounce.300ms="search">
                    </div>
                    <div class="flex-grow-1 order-3 order-md-4 w-md-auto d-none d-lg-block">
                    </div>
                    <div class="order-4 order-md-5">
                        <button class="btn btn-outline-danger flex-shrink-0 p-2 position-relative d-block"
                            href="#invoice-form" data-bs-toggle="modal" data-bs-target="#invoice-modal">
                            <span class="position-absolute top-0 end-100 basket-translate badge rounded-pill bg-danger"
                                x-cloak x-show="basketCount > 0" x-text="basketCount"></span>
                            <span class="d-none d-md-inline pe-1">
                                ثبت پیش فاکتور
                            </span>
                            <i class="bi bi-cart mx-1"></i>
                        </button>
                    </div>
                </div>
                <div class="col-12">
                    <nav class="navbar navbar-expand-lg bg-white py-0">
                        <button class="navbar-toggler my-2 fs-6" type="button" data-bs-toggle="collapse"
                            data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
                            aria-label="Toggle navigation">
                            <span class="navbar-toggler-icon"></span>
                            دسته‌بندی کالاها
                        </button>
                        <div class="collapse navbar-collapse" id="navbarNav">
                            <ul class="navbar-nav flex-row flex-wrap list-unstyled fs-7">
                                <li class="nav-item">
                                    <a class="nav-link category-menu category-menu-active text-nowrap p-2 fw-bold"
                                        href="#" :class="{ 'category-menu-active': selectedCategory === '' }"
                                        @click.prevent="selectCategory('')">
                                        {{ 'همه محصولات' }}
                                    </a>
                                </li>
                                @foreach ($categories as $category)
                                    <li class="nav-item">
                                        <a class="nav-link category-menu text-nowrap p-2" href="#"
                                            :class="{ 'category-menu-active': selectedCategory === '{{ $category['id'] }}' }"
                                            @click.prevent="selectCategory('{{ $category['id'] }}')">{{ $category['name'] }}</a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </nav>
                </div>
            </div>

            <div class="row g-0">
                <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2 border-end border-light-subtle bg-white" x-cloak
                    x-show="categoryFilters.length > 0">
                    <div class="d-flex align-items-center justify-content-between border-bottom p-3">
                        <span class="fw-bold" x-text="selectedCategoryName"></span>
                        <button type="button" class="btn btn-link link-danger p-0 text-decoration-none small"
                            @click="resetFilters()">حذف فیلترها</button>
                    </div>
                    <div class="accordion accordion-flush">
                        <template x-for="(property, index) in categoryFilters" :key="property.property_key">
                            <div class="accordion-item">
                                <h2 class="accordion-header" :id="'filter-heading-' + index">
                                    <button class="accordion-button shadow-none small fw-bold bg-white" type="button"
                                        data-bs-toggle="collapse" :data-bs-target="'#filter-collapse-' + index"
                                        aria-expanded="true" :aria-controls="'filter-collapse-' + index">
                                        <span x-text="property.property_key"></span>
                                        <template x-if="property.unit">
                                            <span class="text-muted fw-normal ms-1"
                                                x-text="'(' + property.unit + ')'"></span>
                                        </template>
                                    </button>
                                </h2>
                                <div :id="'filter-collapse-' + index" class="accordion-collapse collapse show"
                                    :aria-labelledby="'filter-heading-' + index">
                                    <div class="accordion-body pt-0">
                                        <template x-if="filterTypeOf(property) === 'range'">
                                            <div>
                                                <div class="d-flex justify-content-between text-muted small mb-1">
                                                    <span>
                                                        از
                                                        <span class="px-1"
                                                            x-text="activeFilters[property.property_key].values[activeFilters[property.property_key].minIndex]"></span>
                                                        <template x-if="property.unit">
                                                            <span x-text="property.unit"></span>
                                                        </template>
                                                    </span>
                                                    <span>
                                                        تا <span class="px-1"
                                                            x-text="activeFilters[property.property_key].values[activeFilters[property.property_key].maxIndex]"></span>
                                                        <template x-if="property.unit">
                                                            <span x-text="property.unit"></span>
                                                        </template>
                                                    </span>
                                                </div>
                                                <input type="range" class="form-range" step="1" min="0"
                                                    :max="activeFilters[property.property_key].values.length - 1"
                                                    :value="activeFilters[property.property_key].minIndex"
                                                    @input="setRangeIndex(property.property_key, 'min', $event.target.value)">
                                                <input type="range" class="form-range" step="1" min="0"
                                                    :max="activeFilters[property.property_key].values.length - 1"
                                                    :value="activeFilters[property.property_key].maxIndex"
                                                    @input="setRangeIndex(property.property_key, 'max', $event.target.value)">
                                            </div>
                                        </template>
                                        <template x-if="filterTypeOf(property) !== 'range'">
                                            <div>
                                                <template x-for="(option, optionIndex) in property.property_values"
                                                    :key="option.property_value">
                                                    <div class="form-check py-1 mb-0">
                                                        <input class="form-check-input" type="checkbox"
                                                            :id="'filter-option-' + index + '-' + optionIndex"
                                                            :checked="activeFilters[property.property_key].selected.includes(
                                                                option
                                                                .property_value)"
                                                            @change="toggleOption(property.property_key, option.property_value)">
                                                        <label
                                                            class="form-check-label w-100 d-flex justify-content-between"
                                                            :for="'filter-option-' + index + '-' + optionIndex">
                                                            <span x-text="option.property_value"></span>
                                                            <span class="text-secondary small"
                                                                x-text="option.property_value_count"></span>
                                                        </label>
                                                    </div>
                                                </template>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
                <div class="col">
                    <div class="row g-0 row-cols-1"
                        :class="categoryFilters.length > 0 ?
                            'row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-5' :
                            'row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-6'"
                        x-show="visibleCount !== 0">
                        @foreach ($products as $productKey => $product)
                            <div class="col" x-show="isVisible({{ $product['id'] }})">
                                <div class="card rounded-0 h-100 border-0 border-light-subtle border-start border-end">

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
                                                        <span class="carousel-control-prev-icon"
                                                            aria-hidden="true"></span>
                                                        <span class="visually-hidden">Previous</span>
                                                    </button>
                                                    <button class="carousel-control-next" type="button"
                                                        data-bs-target="#product-carousel-{{ $productKey }}"
                                                        data-bs-slide="next">
                                                        <span class="carousel-control-next-icon"
                                                            aria-hidden="true"></span>
                                                        <span class="visually-hidden">Next</span>
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <div class="card-body border-0 border-light-subtle border-bottom">
                                        <h5 class="h6 card-title fw-bold">{{ $product['name'] }}</h5>
                                        <div class="card-text">
                                            @foreach ($product['product_properties'] as $property)
                                                <div>
                                                    <span class="fw-bold">{{ $property['property_key'] }}</span>
                                                    {{ implode(', ', $property['property_values']) }}
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                    @if ($product['packages'])
                                        @foreach ($product['packages'] as $package)
                                            <div
                                                class="card-footer rounded-0 border-bottom border-light-subtle text-body-secondary d-flex flex-column">
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
                                                        <span
                                                            class="ms-1 d-inline-block">{{ $package['guaranty'] }}</span>
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
                                                            class="col-3 btn btn-light text-center border border-secondary-subtle"
                                                            type="button"
                                                            @click="incrementBasket({{ $package['id'] }})">➕</button>
                                                        <input class="col-6 form-control text-center input-spin-none"
                                                            type="number" min="0"
                                                            x-model.number="basket[{{ $package['id'] }}]"
                                                            name="invoice_items[{{ $package['id'] }}][cnt]"
                                                            form="invoice-form">
                                                        @if ($package['unit'])
                                                            <span class="input-group-text">{{ $package['unit'] }}</span>
                                                        @endif
                                                        <button
                                                            class="col-3 btn btn-light text-center border border-secondary-subtle"
                                                            type="button"
                                                            @click="decrementBasket({{ $package['id'] }})">➖</button>
                                                    </div>
                                                @endif
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="col-12 text-center" x-cloak x-show="visibleCount === 0">
                        <div class="text-muted p-5 h4">
                            محصولی که دنبال آن بودید پیدا نشد!
                        </div>
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

        <div class="modal" id="invoice-modal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">ثبت پیش فاکتور</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <x-form method="POST" action="{{ $storeInvoiceAction }}" id="invoice-form"
                            x-on:submit.prevent="submitInvoice($event.target)">
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
                            <x-button-submit :md="8" name="submit" :errors="$errors" :class="'btn-success'"
                                x-bind:disabled="submitting">
                                <span class="spinner-border spinner-border-sm ms-1" role="status"
                                    aria-hidden="true" x-cloak x-show="submitting"></span>
                                ثبت
                            </x-button-submit>
                        </x-form>
                    </div>
                </div>
            </div>
        </div>

        @yield('POS_END')
    </body>

    </html>
@endspaceless
