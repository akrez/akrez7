@extends('layouts.app')

@section('header', __('Packages'))

@php
    $params = [
        'base_url' => route('packages.index'),
    ];
@endphp

@section('content')

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
                    <template x-for="(product, productIndex) in products" :key="'product-id-' + product.id">
                        <template x-for="(package, packageIndex) in packages[product.id]" :key="'package-id-' + package.id">
                            <tr :class="packageIndex === 0 ? 'border-top' : ''">
                                <td x-bind:rowspan="Object.keys(packages[product.id]).length" x-text="product.name"
                                    x-show="packageIndex === 0"></td>
                                <td class="" x-bind:rowspan="Object.keys(packages[product.id]).length" x-text="'+'"
                                    x-show="packageIndex === 0"></td>
                                <td class="">
                                    <input class="form-control p-1 text-center"
                                        x-model="packages[product.id][packageIndex]['price']">
                                </td>
                                <td class="">
                                    <select class="form-select p-1 text-center"
                                        x-model="packages[product.id][packageIndex]['show_price']">
                                        <option :selected="!packages[product.id][packageIndex]['show_price']"
                                            value="">@lang('No')</option>
                                        <option :selected="packages[product.id][packageIndex]['show_price']" value="1">
                                            @lang('Yes')</option>
                                    </select>
                                </td>
                                <td class="">
                                    <input class="form-control p-1 text-center"
                                        x-model="packages[product.id][packageIndex]['status']">
                                </td>
                                <td class="">
                                    <input class="form-control p-1 text-center"
                                        x-model="packages[product.id][packageIndex]['unit']">
                                </td>
                                <td class="">
                                    <select class="form-select p-1 text-center"
                                        x-model="packages[product.id][packageIndex]['color_id']">
                                        <option :value="''"></option>
                                        <template x-for="color in colors" :key="color.id">
                                            <option :selected="color.id == getPackage(product?.id, packageIndex)?.color_id"
                                                :value="color.id" x-text="color.name"
                                                :style="{
                                                    'color': getReverseColorCode(color.code),
                                                    'background-color': color.code,
                                                }">
                                            </option>
                                        </template>
                                    </select>
                                </td>
                                <td class="">
                                    <input class="form-control p-1 text-center"
                                        x-model="packages[product.id][packageIndex]['guaranty']">
                                </td>
                                <td class="">
                                    <input class="form-control p-1 text-center"
                                        x-model="packages[product.id][packageIndex]['description']">
                                </td>
                                <td class=""></td>
                                <td class=""></td>
                            </tr>
                        </template>
                    </template>
                </tbody>
                <tbody x-show="loading.syncLists">
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
                base_url: null,
                products: [],
                packages: [],
                colors: [],
                list: [],
                opened: [],
                loading: {
                    syncLists: false
                },
                async initData(initParams) {
                    this.base_url = initParams.base_url;
                    await this.syncLists();
                },
                getReverseColorCode(colorCode) {
                    colorCode = colorCode.replace('#', '');
                    return '#' + (0xFFFFFF ^ parseInt(colorCode, 16)).toString(16).padStart(6, '0');
                },
                getColorCode(productId, packageIndex, def = null, reverse = false) {
                    colorCode = this.getColorAttr(productId, packageIndex, 'code');
                    if (!colorCode) {
                        return def;
                    }
                    if (!reverse) {
                        return colorCode;
                    }
                    return this.getReverseColorCode(colorCode);
                },
                getColorAttr(productId, packageIndex, attr, def = null) {
                    const packageModel = this.getPackage(productId, packageIndex);
                    if (!packageModel) return def;

                    const colorId = packageModel.color_id;
                    const colorModel = this.colors[colorId];
                    if (!colorModel) return def;

                    const attrValue = colorModel[attr];
                    return attrValue ?? def;
                },
                getPackage(productId, packageIndex) {
                    return this.packages[productId][packageIndex];
                },
                async syncLists() {
                    try {
                        if (this.loading.syncLists) {
                            return;
                        }
                        this.loading.syncLists = true;

                        let res = await fetch(this.base_url + "/list");
                        if (!res.ok) {
                            return;
                        }

                        let json = await res.json();

                        products = json.data.products || [];
                        products.forEach(product => {
                            this.packages[product.id] = [];
                            this.products.push(product);
                        });

                        packages = json.data.packages || [];
                        packages.forEach(package => {
                            this.packages[package.product_id].push(package);
                        });

                        colors = json.data.colors || [];
                        colors.forEach(color => {
                            this.colors[color.id] = color;
                        });



                    } catch (e) {
                        console.log(e);
                    } finally {
                        this.loading.syncLists = false;
                    }
                }
            };
        }
    </script>
@endsection
