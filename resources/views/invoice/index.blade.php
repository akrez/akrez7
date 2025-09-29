@extends('layouts.app')

@section('header', __('Invoices'))
@section('content')
    <form action="{{ route('invoices.index') }}" method="GET" class="row row-cols-lg-auto g-3 align-items-center mb-3">
        <x-input :md="12" :mt="3" :row="false" :errors="$errors" name="invoice_delivery[name]"
            label="{{ __('validation.attributes.invoice_delivery.name') }}" :value="Arr::get($invoice_delivery, 'name')" />
        <x-input :md="12" :mt="3" :row="false" :errors="$errors" name="invoice_delivery[mobile]"
            label="{{ __('validation.attributes.invoice_delivery.mobile') }}" :value="Arr::get($invoice_delivery, 'mobile')" />
        <x-input :md="12" :mt="3" :row="false" :errors="$errors" name="invoice[invoice_status]"
            label="{{ __('validation.attributes.invoice.invoice_status') }}" :value="Arr::get($invoice, 'invoice_status')" type="select"
            :options="['' => ''] + \App\Enums\InvoiceStatusEnum::toArray()" />
        <div class="col-12">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-search"></i>
            </button>
        </div>
    </form>

    @forelse ($invoices as $invoice)
        <div class="row">
            <div class="col-12 mb-3">
                <div class="card">
                    <div class="card-header bg-light">
                        {{ \Arr::get($invoice, 'invoice_uuid') }}
                    </div>
                    <div class="card-body">
                        @include('invoice.__tables', ['invoice' => $invoice])
                        <div class="row mt-3">
                            <div class="col-md-4">
                                <x-form method="PUT" :action="route('invoices.update', ['id' => $invoice['id']])"
                                    class="row row-cols-lg-auto g-3 align-items-center mt-0">
                                    <x-input :md="12" :mt="0" :row="false" :errors="$errors"
                                        name="invoice[invoice_status]"
                                        label="{{ __('validation.attributes.invoice.invoice_status') }}" :value="Arr::get($invoice, 'invoice_status.value')"
                                        type="select" :options="\App\Enums\InvoiceStatusEnum::toArray()" />
                                    <x-button-submit name="submit" :errors="$errors" :row="false" md="12"
                                        mt="0">
                                        {{ __('Edit') }}
                                    </x-button-submit>
                                </x-form>
                            </div>
                            <div class="col-md-8">
                                <div class="row row-cols-1 gy-3">
                                    @foreach ($presentInfos as $presentInfo)
                                        <div class="col">
                                            @php
                                                $invoiceUrl = $presentService->routeByPresentInfo(
                                                    $presentInfo,
                                                    'invoices.show',
                                                    ['invoice_uuid' => $invoice['invoice_uuid']],
                                                    true,
                                                );
                                            @endphp
                                            <div class="input-group mb-0">
                                                @if ($presentInfo == $invoice['present_info'])
                                                    <span class="input-group-text">⭐</span>
                                                @endif
                                                <input type="text" class="form-control text-end" disabled="disabled"
                                                    value="{{ $invoiceUrl }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="row">
            <div class="col-12">
                <div class="alert alert-secondary" role="alert">
                    @lang('Not Found')
                </div>
            </div>
        </div>
    @endforelse

    {{ $paginator->appends(request()->query())->links() }}
@endsection
