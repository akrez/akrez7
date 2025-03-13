<?php

namespace App\View\Components;

use App\View\Components\Component;
use Closure;
use Illuminate\View\View;

class InputText extends Component
{
    public $name;

    public $value;

    public $label;

    public $id;

    public $class;

    public $errors;

    public $hints;

    public $type;

    public function __construct(
        $name,
        $value = '',
        $label = null,
        $id = null,
        $class = null,
        $errors = null,
        $hints = [],
        $type = 'text'
    ) {
        $this->name = $name;
        $this->value = old($name, $value);
        $this->label = ($label === null ? __('validation.attributes.' . $name) : $label);
        $this->id = ($id === null ? crc32($name . $this->label) : $id);
        $this->class = 'form-control ' . $class;
        $this->errors = $errors ? $errors->get($name) : [];
        $this->hints = $hints;
        $this->type = $type;
    }

    public function render(): View|Closure|string
    {
        return view('components.input-text');
    }
}
