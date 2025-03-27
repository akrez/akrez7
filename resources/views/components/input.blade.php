@if ($row)
    <div class="row">
@endif

<div class="col-md-{{ $md }} mt-{{ $mt }}">
    <div class="{{ $label ? 'input-group' : 'form-group' }}">
        @if ($label)
            <span class="input-group-text">{{ $label }}</span>
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
                type="{{ $type }}" value="{{ $value }}" {{ $attributes }} />
        @endif

        @foreach ($errors as $error)
            <div class="invalid-feedback">{{ $error }}</div>
        @endforeach
    </div>

    @foreach ($hints as $hint)
        <small class="form-text">{{ $hint }}</small>
        @if (!$loop->last)
            <br>
        @endif
    @endforeach
</div>

@if ($row)
    </div>
@endif
