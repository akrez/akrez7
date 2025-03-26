@extends('layouts.app')

@section('header', __('Contacts'))

@section('content')
<div class="row mb-2">
    <div class="col-md-2 mt-1">
        <a class="btn btn-light border border-dark w-100" href="{{ route('contacts.create') }}">
            @lang('Create :name', ['name' => __('Contact')])
        </a>
    </div>
</div>
<div class="row">
    <div class="col-md-12 table-responsive">
        <table class="table table-striped table-hover table-bordered align-middle rounded-3 text-center">
            <thead class="table-dark">
                <tr>
                    <th scope="col">@lang('validation.attributes.contact_type')</th>
                    <th scope="col">@lang('validation.attributes.contact_value')</th>
                    <th scope="col">@lang('validation.attributes.contact_link')</th>
                    <th scope="col">@lang('validation.attributes.contact_order')</th>
                    <th scope="col">@lang('validation.attributes.created_at')</th>
                    <th scope="col">@lang('validation.attributes.updated_at')</th>
                    <th scope="col"></th>
                    <th scope="col"></th>
                </tr>
            </thead>
            <tbody dir="ltr">
                @forelse ($contacts as $contact)
                <tr dir="ltr">
                    <td scope="col">{{ \Arr::get($contact, 'contact_type.trans') }}</td>
                    <td scope="col">{{ $contact['contact_value'] }}</td>
                    <td scope="col">{{ $contact['contact_link'] }}</td>
                    <td scope="col">{{ $contact['contact_order'] }}</td>
                    <td scope="col">{{ \Arr::get($contact, 'created_at.fa') }}</td>
                    <td scope="col">{{ \Arr::get($contact, 'updated_at.fa') }}</td>
                    <td>
                        <a class="btn btn-light border border-dark w-100" href="{{ route('contacts.edit', ['id' => $contact['id']]) }}">
                            @lang('Edit')
                        </a>
                    </td>
                    <td>
                        <form action="{{ route('contacts.destroy', ['id' => $contact['id']]) }}" method="post">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger border border-dark w-100">
                                @lang('Delete')
                            </button>
                        </form>
                    </td>
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
