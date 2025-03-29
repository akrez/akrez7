@extends('layouts.app')

@section('header', __('Payvoices'))

@section('content')
    {{ $paginator->links() }}
    <div class="row">
        <div class="col-md-12 table-responsive">
            <table class="table table-striped table-hover table-bordered align-middle rounded-3 text-center">
                <thead class="table-dark">
                    <tr>
                        <th scope="col">IP</th>
                        <th scope="col">Device</th>
                        <th scope="col">Platform</th>
                        <th scope="col">Browser</th>
                        <th scope="col">Robot</th>
                        <th scope="col">Created At</th>
                        <th scope="col">User Agent</th>
                    </tr>
                </thead>
                <tbody dir="ltr">
                    @forelse ($payvoices as $payvoice)
                        <tr dir="ltr">
                            <td scope="col">{{ $payvoice['ip'] }}</td>
                            <td scope="col">
                                @if ($src = $payvoice['useragent_device']['icon'])
                                    <img class="max-height-24-px" src="{{ $src }}">
                                    <br>
                                @endif
                                <p class="m-0">{{ $payvoice['useragent_device']['text'] }}</p>
                            </td>
                            <td scope="col">
                                @if ($src = $payvoice['useragent_platform']['icon'])
                                    <img class="max-height-24-px" src="{{ $src }}">
                                    <br>
                                @endif
                                <p class="m-0">{{ $payvoice['useragent_platform']['text'] }}</p>
                            </td>
                            <td scope="col">
                                @if ($src = $payvoice['useragent_browser']['icon'])
                                    <img class="max-height-24-px" src="{{ $src }}">
                                    <br>
                                @endif
                                <p class="m-0">{{ $payvoice['useragent_browser']['text'] }}</p>
                            </td>
                            <td scope="col">
                                @if ($src = $payvoice['useragent_robot']['icon'])
                                    <img class="max-height-24-px" src="{{ $src }}">
                                    <br>
                                @endif
                                <p class="m-0">{{ $payvoice['useragent_robot']['text'] }}</p>
                            </td>
                            <td scope="col">{{ $payvoice['created_at']['fa'] }}</td>
                            <td scope="col" class="font-monospace-small">{{ $payvoice['useragent'] }}</td>
                        </tr>
                    @empty
                        <tr class="table-warning">
                            <td colspan="99">
                                @lang('Not Found')
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
