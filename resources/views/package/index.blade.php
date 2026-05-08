@extends('layouts.app')

@section('header', __('Packages'))

@php
    $params = [
        'package_statuses' => \App\Enums\PackageStatusEnum::toArray(),
        'show_prices' => ['' => __('No'), '1' => __('Yes')],
        'urls' => [
            'packages' => [
                'list' => route('packages.index') . '/list',
                'store' => route('packages.store'),
                'update' => route('packages.index'),
                'destroy' => route('packages.index'),
            ],
        ],
        'trans' => [
            'Create' => __('Create'),
            'Edit' => __('Edit'),
            'Delete' => __('Delete'),
            'Yes' => __('Yes'),
            'No' => __('No'),
            'Are you sure?' => __('Are you sure?'),
            'Reset' => __('Reset'),
            'validation' => [
                'attributes' => [
                    'price' => __('validation.attributes.price'),
                    'show_price' => __('validation.attributes.show_price'),
                    'status' => __('validation.attributes.status'),
                    'unit' => __('validation.attributes.unit'),
                    'color_id' => __('validation.attributes.color_id'),
                    'guaranty' => __('validation.attributes.guaranty'),
                    'description' => __('validation.attributes.description'),
                ],
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
        <div class="col-12">
            <template x-for="(productId, productIndex) in Object.keys(relations)" :key="'productId-' + '-' + productId">
                <div class="card text-bg-light rounded-0">
                    <div class="card-header d-flex p-0 rounded-0">
                        <span class="flex-grow-1 p-2 text-center" x-text="products[productId].name">
                        </span>
                        <span class="flex-grow-0 p-1 pt-2 border-start btn rounded-0" @click="addEmpty(productId)">
                            <div class="p-0 px-1">➕</div>
                        </span>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle text-center m-0">
                            <thead>
                                <tr class="table-light">
                                    <th class="fw-normal" x-text="trans.validation.attributes.price"></th>
                                    <th class="fw-normal" x-text="trans.validation.attributes.show_price"></th>
                                    <th class="fw-normal" x-text="trans.validation.attributes.status"></th>
                                    <th class="fw-normal" x-text="trans.validation.attributes.unit"></th>
                                    <th class="fw-normal" x-text="trans.validation.attributes.color_id"></th>
                                    <th class="fw-normal" x-text="trans.validation.attributes.guaranty"></th>
                                    <th class="fw-normal" x-text="trans.validation.attributes.description"></th>
                                    <th class="fw-normal"></th>
                                    <th class="fw-normal"></th>
                                    <th class="fw-normal"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(packageId, packageIndex) in relations[productId]"
                                    :key="'packageId-' + '-' + packageId">
                                    <tr :class="packageIndex === 0 ? 'border-top' : ''">
                                        <td
                                            :class="detectBgColor(packageId,
                                                packages_input[packageId]?.price,
                                                packages[packageId]?.price,
                                                'strval')">
                                            <input class="form-control p-1 text-center"
                                                x-bind:value="formattedPrice(packages_input[packageId]['price'])"
                                                @input="packages_input[packageId]['price'] = unformatPrice($event.target.value)">
                                        </td>
                                        <td
                                            :class="detectBgColor(packageId,
                                                packages_input[packageId]?.show_price,
                                                packages[packageId]?.show_price,
                                                'boolval')">
                                            <select class="form-select p-1 text-center"
                                                x-model="packages_input[packageId]['show_price']">
                                                <template x-for="(show_price, show_price_key) in show_prices">
                                                    <option
                                                        :selected="boolval(show_price_key) == boolval(packages_input[packageId]
                                                            .show_price)"
                                                        :value="boolval(show_price_key)" x-text="show_price">
                                                    </option>
                                                </template>
                                            </select>
                                        </td>
                                        <td
                                            :class="detectBgColor(packageId,
                                                packages_input[packageId]?.package_status.value,
                                                packages[packageId]?.package_status.value,
                                                '')">
                                            <select class="form-select p-1 text-center"
                                                x-model="packages_input[packageId].package_status.value">
                                                <template x-for="(package_status, package_status_key) in package_statuses"
                                                    :key="package_status_key">
                                                    <option
                                                        :selected="package_status_key == packages_input[packageId]?.package_status
                                                            ?.value"
                                                        :value="package_status_key" x-text="package_status">
                                                    </option>
                                                </template>
                                            </select>
                                        </td>

                                        <td
                                            :class="detectBgColor(packageId,
                                                packages_input[packageId]?.unit,
                                                packages[packageId]?.unit,
                                                'strval')">
                                            <input class="form-control p-1 text-center" :disabled="!isNewId(packageId)"
                                                x-model="packages_input[packageId]['unit']">
                                        </td>
                                        <td
                                            :class="detectBgColor(packageId,
                                                packages_input[packageId]?.color_id,
                                                packages[packageId]?.color_id,
                                                'parseInt')">
                                            <select class="form-select p-1 text-center" :disabled="!isNewId(packageId)"
                                                x-model="packages_input[packageId]['color_id']">
                                                <option></option>
                                                <template x-for="color in colors" :key="color.id">
                                                    <option :selected="color.id == packages_input[packageId]['color_id']"
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
                                            :class="detectBgColor(packageId,
                                                packages_input[packageId]?.guaranty,
                                                packages[packageId]?.guaranty,
                                                'parseInt')">
                                            <input class="form-control p-1 text-center" :disabled="!isNewId(packageId)"
                                                x-model="packages_input[packageId]['guaranty']">
                                        </td>
                                        <td
                                            :class="detectBgColor(packageId,
                                                packages_input[packageId]?.description,
                                                packages[packageId]?.description,
                                                'strval')">
                                            <input class="form-control p-1 text-center" :disabled="!isNewId(packageId)"
                                                x-model="packages_input[packageId]['description']">
                                        </td>
                                        <td :class="detectBgColor(packageId, null, null, '')">
                                            <div class="btn w-100 p-1 border-dark" @click="persist(packageId)"
                                                :class="isNewId(packageId) ? 'bg-success-subtle' : 'bg-primary-subtle'"
                                                x-text="isNewId(packageId) ? trans.Create : trans.Edit">
                                            </div>
                                        </td>
                                        <td :class="detectBgColor(packageId, null, null, '')">
                                            <div class="btn w-100 p-1 border-dark bg-danger-subtle"
                                                @click="destroy(packageId, productId)" x-text="trans.Delete">
                                            </div>
                                        </td>
                                        <td :class="detectBgColor(packageId, null, null, '')">
                                            <div class="btn w-100 p-1 border-dark bg-warning-subtle"
                                                @click="reset(packageId, productId)" x-text="trans.Reset">
                                            </div>
                                        </td>
                                    </tr>
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
            </template>
        </div>
    </div>

    <script>
        function data() {
            return {
                urls: null,
                trans: [],
                products: [],
                packages_input: [],
                packages: [],
                colors: [],
                list: [],
                package_statuses: [],
                show_prices: [],
                relations: [],
                loading: {
                    indexPackages: false,
                    updatePackage: false,
                    destroyPackage: false,
                },
                formattedPrice(v) {
                    if (v === null || v === undefined || v === '') return '';
                    const s = String(v);
                    return s.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                },
                unformatPrice(str) {
                    const digits = String(str).replace(/,/g, '').replace(/[^\d]/g, '');
                    return digits === '' ? '' : Number(digits);
                },
                addEmpty(productId) {
                    id = 'id-' + (Math.random() * 100000);
                    psv = Object.keys(this.package_statuses)[0];
                    psn = this.package_statuses[psv];
                    data = {
                        id: id,
                        product_id: productId,
                        price: null,
                        show_price: null,
                        package_status: {
                            name: psn,
                            value: psv
                        },
                        unit: null,
                        color_id: null,
                        guaranty: null,
                        description: null,
                    };
                    this.pushRelation(data);
                },
                reset(packageId, productId) {
                    this.packages_input[packageId] = this.cloneJson(this.packages[packageId]);
                },
                persist(packageId) {
                    data = {
                        product_id: this.packages_input[packageId].product_id,
                        price: this.packages_input[packageId].price,
                        show_price: this.packages_input[packageId].show_price,
                        package_status: this.packages_input[packageId].package_status.value,
                        unit: this.packages_input[packageId].unit,
                        color_id: this.packages_input[packageId].color_id,
                        guaranty: this.packages_input[packageId].guaranty,
                        description: this.packages_input[packageId].description,
                    };

                    if (this.isNewId(packageId)) {
                        this.storePackage(data, packageId);
                    } else {
                        this.updatePackage(data, packageId);
                    }
                },
                destroy(packageId, productId) {
                    that = this;
                    this.alertConfirm(
                        that.trans['Are you sure?'],
                        function(result) {
                            if (!result.isConfirmed) {
                                return;
                            }
                            if (that.isNewId(packageId)) {
                                that.removeRelation(packageId, productId);
                            } else {
                                that.destroyPackage(packageId, productId);
                            }
                        }
                    );
                },
                isNumeric(v) {
                    return (v !== null && v !== "" && !Number.isNaN(Number(v)));
                },
                isNewId(packageId) {
                    return !this.isNumeric(packageId);
                },
                async initData(initParams) {
                    this.urls = initParams.urls;
                    this.package_statuses = initParams.package_statuses;
                    this.show_prices = initParams.show_prices;
                    this.trans = initParams.trans;
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
                async storePackage(data, tempId) {
                    try {
                        if (this.loading.storePackage) return;
                        this.loading.storePackage = true;

                        const gameRes = await fetch(this.urls.packages.store, {
                            method: 'POST',
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
                            this.replceRelation(gameResJson.data.package, tempId);
                        } else {
                            this.alertError(gameResJson.message);
                        }

                    } catch (err) {
                        console.log(err);
                        this.alertError('خطا');
                    } finally {
                        this.loading.storePackage = false;
                    }
                },
                async destroyPackage(id, productId) {
                    try {
                        if (this.loading.destroyPackage) return;
                        this.loading.destroyPackage = true;

                        const formData = new FormData();
                        formData.append('_method', 'DELETE');

                        const gameRes = await fetch(this.urls.packages.destroy + '/' + id, {
                            method: 'POST',
                            body: formData
                        });

                        const gameResJson = await gameRes.json();

                        if (gameRes.ok) {
                            this.alertSuccess(gameResJson.message);
                            this.removeRelation(id, productId);
                        } else {
                            this.alertError(gameResJson.message);
                        }

                    } catch (err) {
                        console.log(err);
                        this.alertError('خطا');
                    } finally {
                        this.loading.destroyPackage = false;
                    }
                },
                async updatePackage(data, id) {
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
                            this.setPackage(gameResJson.data.package);
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
                setPackage(package) {
                    this.packages_input[package.id] = this.cloneJson(package);
                    this.packages[package.id] = this.cloneJson(package);
                },
                pushRelation(package) {
                    this.setPackage(package);
                    this.relations[package.product_id].push(package.id);
                },
                replceRelation(package, oldId) {
                    this.setPackage(package);
                    index = this.relations[package.product_id].indexOf(oldId);
                    if (index === -1) return;
                    this.relations[package.product_id][index] = package.id;
                },
                removeRelation(packageId, productId) {
                    if (!packageId) return;
                    index = this.relations[productId].indexOf(packageId);
                    if (index === -1) return;
                    this.relations[productId].splice(index, 1);
                },
                async indexPackages() {
                    try {
                        if (this.loading.indexPackages) return;
                        this.loading.indexPackages = true;

                        let res = await fetch(this.urls.packages.list);
                        if (!res.ok) return;

                        let json = await res.json();

                        products = json.data.products || [];
                        packages = json.data.packages || [];
                        colors = json.data.colors || [];

                        products.forEach(product => {
                            this.products[product.id] = product;
                            this.relations[product.id] = [];
                        });

                        packages.forEach(package => {
                            this.pushRelation(package);
                        });

                        colors.forEach(color => {
                            this.colors[color.id] = color;
                        });

                    } catch (e) {
                        console.log(e);
                        this.alertError('خطا');
                    } finally {
                        this.loading.indexPackages = false;
                    }
                },
                detectBgColor(packageId, newValue, oldValue, fnc) {
                    if (this.isNewId(this.packages_input[packageId].id)) {
                        return 'bg-success-subtle';
                    }

                    if (
                        (fnc === '' && (oldValue) !== (newValue)) ||
                        (fnc === 'strval' && this.strval(oldValue) !== this.strval(newValue)) ||
                        (fnc === 'boolval' && this.boolval(oldValue) !== this.boolval(newValue))
                    ) {
                        return 'bg-info-subtle';
                    }

                    if (this.packages[packageId]?.package_status.value === 'deactive') {
                        return 'bg-danger-subtle';
                    }

                    if (this.packages[packageId]?.package_status.value === 'out_of_stock') {
                        return 'bg-warning-subtle';
                    }

                    return '';
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
                alertConfirm(text, func) {
                    that = this;
                    Swal.fire({
                        text: text,
                        icon: "warning",
                        showCancelButton: true,
                        confirmButtonColor: "red",
                        confirmButtonText: that.trans.Yes,
                        cancelButtonText: that.trans.No,
                        position: 'center',
                    }).then((result) => func(result));
                }
            };
        }
    </script>
@endsection
