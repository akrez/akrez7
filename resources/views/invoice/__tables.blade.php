<table class="table table-bordered m-0 w-100">
    <tbody>
        <tr>
            <td class="table-light">@lang('validation.attributes.invoice_delivery.name')</td>
            <td>{{ \Arr::get($invoice, 'invoiceDelivery.name') }}</td>
            <td class="table-light">@lang('validation.attributes.updated_at')</td>
            <td>{{ $invoice['updated_at']['fa'] }}</td>
            <td class="table-light">@lang('validation.attributes.created_at')</td>
            <td>{{ $invoice['created_at']['fa'] }}</td>
        </tr>
        <tr>
            <td class="table-light">@lang('validation.attributes.invoice_delivery.mobile')</td>
            <td>{{ \Arr::get($invoice, 'invoiceDelivery.mobile') }}</td>
            <td class="table-light">@lang('validation.attributes.invoice.invoice_description')</td>
            <td colspan="3">{{ \Arr::get($invoice, 'invoice_description') }}</td>
        </tr>
        <tr>
            <td class="table-light">@lang('validation.attributes.invoice_delivery.city')</td>
            <td>{{ \Arr::get($invoice, 'invoiceDelivery.city') }}</td>
            <td class="table-light">@lang('validation.attributes.invoice_delivery.address')</td>
            <td colspan="3">{{ \Arr::get($invoice, 'invoiceDelivery.address') }}</td>
        </tr>
    </tbody>
</table>
<table class="table table-bordered m-0 w-100 mt-3">
    <thead class="table-light">
        <tr>
            <th scope="col">@lang('validation.attributes.name')</th>
            <th scope="col">@lang('validation.attributes.guaranty')</th>
            <th scope="col">@lang('validation.attributes.color_id')</th>
            <th scope="col">@lang('validation.attributes.description')</th>
            <th scope="col">@lang('validation.attributes.unit')</th>
            <th scope="col">@lang('validation.attributes.cnt')</th>
            <th scope="col">@lang('validation.attributes.price')</th>
            <th scope="col">@lang('total :name', ['name' => ''])</th>
        </tr>
    </thead>
    <tbody>
        @php
            $totalCnt = 0;
            $totalPrice = 0;
        @endphp
        @foreach ($invoice['invoiceItems'] as $invoiceItem)
            @php
                $totalCnt += $invoiceItem['cnt'];
                $totalPrice += $invoiceItem['cnt'] * $invoiceItem['price'];
            @endphp
            <tr>
                <th>{{ $invoiceItem['package']['product']['name'] ?? '' }}</th>
                <td>{{ $invoiceItem['package']['guaranty'] }}</td>
                <td>
                    <span class="d-inline-block rounded"
                        style="border: 1px black solid; background-color: {{ $invoiceItem['package']['color']['code'] }};">⠀⠀⠀</span>
                    <span class="d-inline-block ms-1">{{ $invoiceItem['package']['color']['name'] }}</span>
                </td>
                <td>{{ $invoiceItem['package']['description'] }}</td>
                <td>{{ $invoiceItem['package']['unit'] }}</td>
                <td>{{ $invoiceItem['cnt'] }}</td>
                <td>{{ number_format($invoiceItem['price']) }}</td>
                <td>{{ number_format($invoiceItem['cnt'] * $invoiceItem['price']) }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot class="table-light">
        <tr>
            <td colspan="4"></td>
            <td class="fw-bold">@lang('total :name', ['name' => __('validation.attributes.cnt')])</td>
            <td>{{ number_format($totalCnt) }}</td>
            <td class="fw-bold">@lang('total :name', ['name' => __('validation.attributes.price')])</td>
            <td>{{ number_format($totalPrice) }}</td>
        </tr>
    </tfoot>
</table>
