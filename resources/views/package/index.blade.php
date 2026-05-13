@extends('layouts.app')

@section('header', __('Packages'))

@php
    $params = [
        'enums' => [
            'package_statuses' => \App\Enums\PackageStatusEnum::toArray(),
            'show_prices' => ['' => __('No'), '1' => __('Yes')],
        ],
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
            <table class="table table-borderless align-middle text-center m-0 table-sm">
                <thead class="sticky-top">
                    <tr class="table-light">
                        <th class="fw-normal"></th>
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
                <tbody x-show="!loading.indexPackages">
                    <template x-for="product in products" :key="'productId-' + '-' + product.id">
                        <template x-for="(package ,packageIndex) in relations[product.id]"
                            :key="'packageId-' + '-' + package.id">
                            <tr :class="packageIndex === 0 ? 'border-top' : ''">
                                <template x-if="packageIndex === 0">
                                    <td x-bind:rowspan="relations[product.id].length"
                                        :class="(product.product_status.value == 'deactive' ? 'bg-danger-subtle' : '')">
                                        <div class="d-flex flex-row">
                                            <span class="flex-grow-1 p-0 text-center" x-text="products[product.id].name">
                                            </span>
                                            <span class="flex-grow-0 p-0 btn rounded-0" @click="addEmpty(product.id)">
                                                <div class="p-0 px-1">➕</div>
                                            </span>
                                        </div>
                                    </td>
                                </template>
                                <td
                                    :class="renderBgColor(product.id, package.id,
                                        package?.price,
                                        packages[package.id]?.price,
                                        'strval')">
                                    <input class="form-control p-1 text-center" x-bind:value="renderPrice(package['price'])"
                                        @input="package['price'] = unformatPrice($event.target.value)">
                                </td>
                                <td
                                    :class="renderBgColor(product.id, package.id,
                                        package?.show_price,
                                        packages[package.id]?.show_price,
                                        'boolval')">
                                    <select class="form-select p-1 text-center" x-model="package['show_price']">
                                        <template x-for="(show_price, show_price_key) in enums.show_prices">
                                            <option
                                                :selected="boolval(show_price_key) == boolval(package
                                                    .show_price)"
                                                :value="boolval(show_price_key)" x-text="show_price">
                                            </option>
                                        </template>
                                    </select>
                                </td>
                                <td
                                    :class="renderBgColor(product.id, package.id,
                                        package?.package_status.value,
                                        packages[package.id]?.package_status.value,
                                        '')">
                                    <select class="form-select p-1 text-center" x-model="package.package_status.value">
                                        <template x-for="(package_status, package_status_key) in enums.package_statuses"
                                            :key="package_status_key">
                                            <option
                                                :selected="package_status_key == package?.package_status
                                                    ?.value"
                                                :value="package_status_key" x-text="package_status">
                                            </option>
                                        </template>
                                    </select>
                                </td>

                                <td
                                    :class="renderBgColor(product.id, package.id,
                                        package?.unit,
                                        packages[package.id]?.unit,
                                        'strval')">
                                    <input class="form-control p-1 text-center" :disabled="!isNewId(package.id)"
                                        x-model="package['unit']">
                                </td>
                                <td
                                    :class="renderBgColor(product.id, package.id,
                                        package?.color_id,
                                        packages[package.id]?.color_id,
                                        'parseInt')">
                                    <select class="form-select p-1 text-center" :disabled="!isNewId(package.id)"
                                        x-model="package['color_id']">
                                        <option></option>
                                        <template x-for="color in colors" :key="color.id">
                                            <option :selected="color.id == package['color_id']" :value="color.id"
                                                x-text="color.name"
                                                :style="{
                                                    'color': getReverseColorCode(color.code),
                                                    'background-color': color.code,
                                                }">
                                            </option>
                                        </template>
                                    </select>
                                </td>
                                <td
                                    :class="renderBgColor(product.id, package.id,
                                        package?.guaranty,
                                        packages[package.id]?.guaranty,
                                        'parseInt')">
                                    <input class="form-control p-1 text-center" :disabled="!isNewId(package.id)"
                                        x-model="package['guaranty']">
                                </td>
                                <td
                                    :class="renderBgColor(product.id, package.id,
                                        package?.description,
                                        packages[package.id]?.description,
                                        'strval')">
                                    <input class="form-control p-1 text-center" :disabled="!isNewId(package.id)"
                                        x-model="package['description']">
                                </td>
                                <td :class="renderBgColor(product.id, package.id, null, null, '')">
                                    <div class="btn w-100 p-1 border-dark" @click="persist(package.id, product.id)"
                                        :class="isNewId(package.id) ? 'bg-success-subtle' : 'bg-primary-subtle'"
                                        x-text="isNewId(package.id) ? trans.Create : trans.Edit">
                                    </div>
                                </td>
                                <td :class="renderBgColor(product.id, package.id, null, null, '')">
                                    <div class="btn w-100 p-1 border-dark bg-warning-subtle"
                                        @click="reset(package.id, product.id)" x-text="trans.Reset">
                                    </div>
                                </td>
                                <td :class="renderBgColor(product.id, package.id, null, null, '')">
                                    <div class="btn w-100 p-1 border-dark bg-danger-subtle"
                                        @click="destroy(package.id, product.id)" x-text="trans.Delete">
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </template>
                </tbody>
                <tbody x-show="loading.indexPackages">
                    <tr>
                        <td colspan="99" class="p-4">
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
                trans: [],
                products: [],
                packages: [],
                colors: [],
                list: [],
                enums: [],
                relations: [],
                loading: {
                    indexPackages: false,
                    updatePackage: false,
                    destroyPackage: false,
                },
                getEmpty(productId, packageId = null) {
                    psv = Object.keys(this.enums.package_statuses)[0];
                    psn = this.enums.package_statuses[psv];
                    return {
                        id: packageId ?? 'id-' + (Math.random() * 100000),
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
                },
                addEmpty(productId) {
                    this.addRelation(this.getEmpty(productId));
                },
                reset(packageId, productId) {
                    index = this.getRelationIndex(productId, packageId);
                    if (index === -1) return '';
                    if (this.isNewId(packageId)) {
                        this.relations[productId][index] = this.getEmpty(productId, packageId);
                    } else {
                        this.relations[productId][index] = this.cloneJson(this.packages[packageId]);
                    }
                },
                async persist(packageId, productId) {
                    index = this.getRelationIndex(productId, packageId);
                    if (index === -1) return '';
                    package = this.relations[productId][index];
                    data = {
                        product_id: package.product_id,
                        price: package.price,
                        show_price: package.show_price,
                        package_status: package.package_status.value,
                        unit: package.unit,
                        color_id: package.color_id,
                        guaranty: package.guaranty,
                        description: package.description,
                    };

                    if (this.isNewId(packageId)) {
                        this.callStorePackage(data, packageId);
                    } else {
                        this.callUpdatePackage(data, packageId);
                    }
                },
                destroy(packageId, productId) {
                    that = this;
                    this.alertConfirm(
                        that.trans['Are you sure?'],
                        async function(result) {
                            if (!result.isConfirmed) {
                                return;
                            }
                            if (!that.isNewId(packageId)) {
                                gameRes = await that.callDestroyPackage(packageId, productId);
                                if (!gameRes.ok) {
                                    return;
                                }
                            }
                            that.removeRelation(packageId, productId);
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
                    this.enums = initParams.enums;
                    this.trans = initParams.trans;
                    await this.callIndexPackages();
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
                getRelationIndex(productId, packageId) {
                    return this.relations[productId].findIndex(package => package.id === packageId);
                },
                addPackage(package) {
                    this.packages[package.id] = package;
                },
                addRelation(package, oldId = null) {
                    index = this.getRelationIndex(package.product_id, package.id);
                    if (index !== -1) {
                        this.relations[package.product_id][index] = this.cloneJson(package);
                        return;
                    }
                    if (oldId) {
                        index = this.getRelationIndex(package.product_id, oldId);
                        if (index !== -1) {
                            this.relations[package.product_id][index] = this.cloneJson(package);
                            return;
                        }
                    }
                    this.relations[package.product_id].push(this.cloneJson(package));
                },
                removeRelation(packageId, productId) {
                    if (!packageId) return;
                    index = this.getRelationIndex(productId, packageId);
                    if (index === -1) return;
                    this.relations[productId].splice(index, 1);
                },
                async callStorePackage(data, tempId) {
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
                            this.addPackage(gameResJson.data.package);
                            this.addRelation(gameResJson.data.package, tempId);
                        } else {
                            this.renderCallError(gameResJson);
                        }

                    } catch (err) {
                        console.log(err);
                        this.alertError('خطا');
                    } finally {
                        this.loading.storePackage = false;
                    }
                },
                async callDestroyPackage(id, productId) {
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
                        } else {
                            this.renderCallError(gameResJson);
                        }

                        return gameRes;

                    } catch (err) {
                        console.log(err);
                        this.alertError('خطا');
                    } finally {
                        this.loading.destroyPackage = false;
                    }
                },
                async callUpdatePackage(data, id) {
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
                            this.addPackage(gameResJson.data.package);
                            this.addRelation(gameResJson.data.package);
                        } else {
                            this.renderCallError(gameResJson);
                        }

                    } catch (err) {
                        console.log(err);
                        this.alertError('خطا');
                    } finally {
                        this.loading.updatePackage = false;
                    }
                },
                async callIndexPackages() {
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
                            this.addPackage(package);
                            this.addRelation(package);
                        });

                        colors.forEach(color => {
                            this.colors[color.id] = color;
                        });

                        this.relations.forEach((relation, productId) => {
                            if (relation.length == 0) {
                                this.addEmpty(productId);
                            }
                        });

                    } catch (e) {
                        console.log(e);
                        this.alertError('خطا');
                    } finally {
                        this.loading.indexPackages = false;
                    }
                },
                renderBgColor(productId, packageId, newValue, oldValue, fnc) {
                    index = this.getRelationIndex(productId, packageId);
                    if (index === -1) return '';

                    if (this.isNewId(this.relations[productId][index].id)) {
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
                renderCallError(res) {
                    const items = Object.entries(res.errors || {}).flatMap(([field, messages]) => {
                        return (messages || []).map(msg => `${field}: ${msg}`);
                    });
                    const html = (items ?
                        `<ul class="text-start m-0"> ${items.map(i => `<li>${i}</li>`).join("")} </ul>` :
                        ``);
                    this.alertError(res.message, html);
                },
                renderPrice(v) {
                    if (v === null || v === undefined || v === '') return '';
                    const s = String(v);
                    return s.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
                },
                unformatPrice(str) {
                    const digits = String(str).replace(/,/g, '').replace(/[^\d]/g, '');
                    return digits === '' ? '' : Number(digits);
                },
                alertError(title, html) {
                    Swal.fire({
                        title: title,
                        icon: 'error',
                        showCloseButton: true,
                        showConfirmButton: false,
                        timerProgressBar: true,
                        html: html,
                        position: 'center',
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
