<?php

namespace App\View\Components;

use Closure;
use Illuminate\View\View;

class Input extends Component
{
    public $name;

    public $value;

    public $label;

    public $id;

    public $errors;

    public $class;

    public $hints;

    public $row;

    public $md;

    public $mt;

    public $type;

    public $rows;

    public $options;

    public function __construct(
        $name,
        $value = '',
        $label = null,
        $id = null,
        $errors = null,
        $class = null,
        $hints = [],
        $row = true,
        $md = 4,
        $mt = 2,
        $type = 'text',
        $rows = 4,
        $options = []
    ) {
        $this->name = $name;
        $this->value = old($name, $value);
        $this->label = ($label === null ? __('validation.attributes.'.$name) : $label);
        $this->id = ($id === null ? crc32($name.$this->label) : $id);
        $this->errors = ($errors ? $errors->get($name) : []);
        $this->class = 'form-control '.($this->errors ? 'is-invalid ' : '').$class;
        $this->hints = $hints;
        $this->row = $row;
        $this->md = $md;
        $this->mt = $mt;
        $this->type = $type;
        $this->rows = $rows;
        $this->options = $options;
    }

    public function render(): View|Closure|string
    {
        return view('components.input');
    }
}
