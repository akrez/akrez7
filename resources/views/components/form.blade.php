<form action="{{ $action }}" enctype="multipart/form-data" method="{{ $method }}" {{ $attributes }}>
    @csrf
    @if ($_method)
        @method($_method)
    @endif
    {{ $slot }}
</form>
