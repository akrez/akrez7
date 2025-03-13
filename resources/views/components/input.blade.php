@if ($row)
    <div class="row">
@endif

<div class="col-md-{{ $md }} mt-{{ $mt }}">
    <div class="form-group">
        @if ($label)
            <label class="form-label" for="{{ $id }}">{{ $label }}</label>
        @endif

        @if ('textarea' === $type)
            <textarea name="{{ $name }}" id="{{ $id }}" class="{{ $class }}" rows="{{ $rows }}">{{ $value }}</textarea>
        @elseif('select' === $type)
            <select name="{{ $name }}" id="{{ $id }}" class="{{ $class }}">
                @foreach ($options as $optionValue => $option)
                    <option value="{{ $optionValue }}" {{ $value == $optionValue ? ' selected ' : '' }}>
                        {{ $option }}
                    </option>
                @endforeach
            </select>
        @else
            <input name="{{ $name }}" id="{{ $id }}" class="{{ $class }}"
                type="{{ $type }}" value="{{ $value }}" />
        @endif

        @foreach ($hints as $hint)
            <small class="form-text">{{ $hint }}</small>
            @if (!$loop->last)
                <br>
            @endif
        @endforeach

        @foreach ($errors as $error)
            <div class="invalid-feedback">{{ $error }}</div>
        @endforeach
    </div>
</div>

@if ($row)
    </div>
@endif
