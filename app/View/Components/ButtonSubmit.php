<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;

class ButtonSubmit extends Component
{
    public $name;

    public $id;

    public $class;

    public $row;

    public $md;

    public $mt;

    public function __construct(
        $name = null,
        $id = null,
        $class = null,
        $row = true,
        $md = 4,
        $mt = 4
    ) {
        $this->name = $name;
        $this->id = ($id === null ? uniqid(strval($name)) : $id);
        $this->class = 'btn w-100 '.($class === null ? 'btn-primary' : $class);
        $this->row = $row;
        $this->md = $md;
        $this->mt = $mt;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.button-submit');
    }
}
