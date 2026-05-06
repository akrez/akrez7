@extends('layouts.app')

@section('header', __('Packages'))

@php
    $params = [
        'package_statuses' => \App\Enums\PackageStatusEnum::toArray(),
        'show_prices' => ['' => __('No'), '1' => __('Yes')],
        'urls' => [
            'packages' => [
                'list' => route('packages.index') . '/list',
                'update' => route('packages.index'),
            ],
        ],
    ];
@endphp

@section('content')
    <style>
        .form-select {
            --bs-form-select-bg-img: none;
        }
    </style>

    <div class="row" x-data="data()" x-init="initData({{ json_encode($params) }})">
        <div class="table-responsive">
            <table class="table table-borderless align-middle rounded-3 text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col"></th>
                        <th scope="col"></th>
                        <th scope="col">@lang('validation.attributes.price')</th>
                        <th scope="col">@lang('validation.attributes.show_price')</th>
                        <th scope="col">@lang('validation.attributes.status')</th>
                        <th scope="col">@lang('validation.attributes.unit')</th>
                        <th scope="col">@lang('validation.attributes.color_id')</th>
                        <th scope="col">@lang('validation.attributes.guaranty')</th>
                        <th scope="col">@lang('validation.attributes.description')</th>
                        <th scope="col"></th>
                        <th scope="col"></th>
                    </tr>
                </thead>
                <tbody>
                    <template x-for="(productId, productIndex) in Object.keys(productIdToPackageIds)"
                        :key="'product-id-' + '-' + (Math.random())">
                        <template x-for="(packageId, packageIndex) in productIdToPackageIds[productId]"
                            :key="'package-id-' + '-' + productId + '-' + packageId">
                            <tr :class="packageIndex === 0 ? 'border-top' : ''">
                                <td x-bind:rowspan="productIdToPackageIds[productId].length"
                                    x-text="products[productId].name" x-show="packageIndex === 0"></td>
                                <td x-bind:rowspan="productIdToPackageIds[productId].length" x-text="'+'"
                                    x-show="packageIndex === 0"></td>
                                <td
                                    :class="{
                                        'bg-info': strval(packages[packageId]['price']) !==
                                            strval(packages_const[packageId]['price'])
                                    }">
                                    <input class="form-control p-1 text-center" x-model="packages[packageId]['price']">
                                </td>

                                <td
                                    :class="{
                                        'bg-info': boolval(packages[packageId]['show_price']) !=
                                            boolval(packages_const[packageId]['show_price'])
                                    }">
                                    <select class="form-select p-1 text-center" x-model="packages[packageId]['show_price']">
                                        <template x-for="(show_price, show_price_key) in show_prices">
                                            <option
                                                :selected="boolval(show_price_key) == boolval(packages[packageId].show_price)"
                                                :value="boolval(show_price_key)" x-text="show_price">
                                            </option>
                                        </template>
                                    </select>
                                </td>
                                <td
                                    :class="{
                                        'bg-info': packages[packageId]['package_status']['value'] !==
                                            packages_const[packageId]['package_status']['value']
                                    }">
                                    <select class="form-select p-1 text-center"
                                        x-model="packages[packageId]['package_status']['value']">
                                        <template x-for="(package_status, package_status_key) in package_statuses"
                                            :key="package_status_key">
                                            <option
                                                :selected="package_status_key == packages[packageId]['package_status']['value']"
                                                :value="package_status_key" x-text="package_status">
                                            </option>
                                        </template>
                                    </select>
                                </td>


                                <td
                                    :class="{
                                        'bg-info': (packages[packageId]['unit']) !==
                                            (packages_const[packageId]['unit'])
                                    }">
                                    <input class="form-control p-1 text-center" x-model="packages[packageId]['unit']">
                                </td>
                                <td
                                    :class="{
                                        'bg-info': parseInt(packages[packageId]['color_id']) !==
                                            parseInt(packages_const[packageId]['color_id'])
                                    }">
                                    <select class="form-select p-1 text-center" x-model="packages[packageId]['color_id']">
                                        <option></option>
                                        <template x-for="color in colors" :key="color.id">
                                            <option :selected="color.id == packages[packageId]['color_id']"
                                                :value="color.id" x-text="color.name"
                                                :style="{
                                                    'color': getReverseColorCode(color.code),
                                                    'background-color': color.code,
                                                }">
                                            </option>
                                        </template>
                                    </select>
                                </td>
                                <td
                                    :class="{
                                        'bg-info': strval(packages[packageId]['guaranty']) !=
                                            strval(packages_const[packageId]['guaranty'])
                                    }">
                                    <input class="form-control p-1 text-center" x-model="packages[packageId]['guaranty']">
                                </td>
                                <td
                                    :class="{
                                        'bg-info': strval(packages[packageId]['description']) !=
                                            strval(packages_const[packageId]['description'])
                                    }">
                                    <input class="form-control p-1 text-center"
                                        x-model="packages[packageId]['description']">
                                </td>
                                <td class="">
                                    <div class="btn btn-primary p-1" @click="persist(packageId)">@lang('Update')</div>
                                </td>
                                <td class=""></td>
                            </tr>
                        </template>
                    </template>
                </tbody>
                <tbody x-show="loading.indexPackages">
                    <tr>
                        <td colspan="99">
                            <div class="spinner-border text-secondary"></div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <script>
        function data() {
            return {
                urls: null,
                products: [],
                packages: [],
                packages_const: [],
                colors: [],
                list: [],
                package_statuses: [],
                show_prices: [],
                productIdToPackageIds: [],
                loading: {
                    indexPackages: false,
                    updatePackage: false,
                },
                persist(packageId) {
                    data = {
                        product_id: this.packages[packageId].product_id,
                        price: this.packages[packageId].price,
                        show_price: this.packages[packageId].show_price,
                        package_status: this.packages[packageId].package_status.value,
                        unit: this.packages[packageId].unit,
                        color_id: this.packages[packageId].color_id,
                        guaranty: this.packages[packageId].guaranty,
                        description: this.packages[packageId].description,
                    };

                    this.updatePackage(packageId, data);
                },
                async initData(initParams) {
                    this.urls = initParams.urls;
                    this.package_statuses = initParams.package_statuses;
                    this.show_prices = initParams.show_prices;
                    await this.indexPackages();
                },
                getReverseColorCode(colorCode) {
                    colorCode = colorCode.replace('#', '');
                    return '#' + (0xFFFFFF ^ parseInt(colorCode, 16)).toString(16).padStart(6, '0');
                },
                cloneJson(obj) {
                    return JSON.parse(JSON.stringify(obj));
                },
                boolval(value) {
                    if (value === 'false') return 0;
                    if (value === 'null') return 0;
                    if (value === '0') return 0;
                    return Boolean(value) ? 1 : 0;
                },
                strval(value) {
                    if (value === null) return '';
                    return String(value);
                },
                async updatePackage(id, data) {
                    try {
                        if (this.loading.updatePackage) return;
                        this.loading.updatePackage = true;

                        const gameRes = await fetch(this.urls.packages.update + '/' + id, {
                            method: 'PUT',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: JSON.stringify(data)
                        });

                        const gameResJson = await gameRes.json();

                        if (gameRes.ok) {
                            this.alertSuccess(gameResJson.message);
                            this.syncPackage(gameResJson.data.package, false);
                        } else {
                            this.alertError(gameResJson.message);
                        }

                    } catch (err) {
                        console.log(err);
                        this.alertError('خطا');
                    } finally {
                        this.loading.updatePackage = false;
                    }
                },
                syncPackage(package, add = true) {
                    this.packages[package.id] = this.cloneJson(package);
                    this.packages_const[package.id] = this.cloneJson(package);
                    if (add) {
                        this.productIdToPackageIds[package.product_id].push(package.id);
                    }
                },
                async indexPackages() {
                    try {
                        if (this.loading.indexPackages) {
                            return;
                        }
                        this.loading.indexPackages = true;

                        let res = await fetch(this.urls.packages.list);
                        if (!res.ok) {
                            return;
                        }

                        let json = await res.json();

                        products = json.data.products || [];
                        packages = json.data.packages || [];
                        colors = json.data.colors || [];

                        this.productIdToPackageIds = [];

                        products.forEach(product => {
                            this.products[product.id] = product;
                            this.productIdToPackageIds[product.id] = [];
                        });

                        packages.forEach(package => {
                            this.syncPackage(package);
                        });

                        colors.forEach(color => {
                            this.colors[color.id] = color;
                        });

                    } catch (e) {
                        console.log(e);
                    } finally {
                        this.loading.indexPackages = false;
                    }
                },
                alertError(text) {
                    Swal.fire({
                        text: text,
                        icon: 'error',
                        timer: 1500,
                        showCloseButton: true,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        toast: true,
                        position: 'bottom',
                    });
                },
                alertSuccess(text) {
                    Swal.fire({
                        text: text,
                        icon: 'success',
                        timer: 1500,
                        showCloseButton: true,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        toast: true,
                        position: 'bottom',
                    });
                },
            };
        }
    </script>
@endsection
