<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;

class Select extends Component
{
    public $name;

    public $value;

    public $label;

    public $id;

    public $class;

    public $errors;

    public $hints;

    public $options;

    public function __construct(
        $name,
        $value = '',
        $label = null,
        $id = null,
        $class = null,
        $errors = null,
        $hints = [],
        $options = []
    ) {
        $this->name = $name;
        $this->value = old($name, $value);
        $this->label = ($label === null ? __('validation.attributes.' . $name) : $label);
        $this->id = ($id === null ? crc32($name . $this->label) : $id);
        $this->class = 'form-control ' . $class;
        $this->errors = $errors ? $errors->get($name) : [];
        $this->hints = $hints;
        $this->options = $options;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.select');
    }
}
