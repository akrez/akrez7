<form action="{{ $action }}" enctype="multipart/form-data" method="{{ $method }}">
    @csrf
    @if ($_method)
        @method($_method)
    @endif
    {{ $slot }}
</form>
