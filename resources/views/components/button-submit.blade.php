@if ($row)
    <div class="row">
@endif

<div class="col-md-{{ $md }} mt-{{ $mt }}">
    <div class="form-group">
        @if ($label)
            <label class="form-label" for="{{ $id }}">{{ $label }}</label>
        @endif
        <button name="{{ $name }}" id="{{ $id }}" class="{{ $class }}"
            type="submit">{{ $slot }}</button>
    </div>
</div>

@if ($row)
    </div>
@endif
