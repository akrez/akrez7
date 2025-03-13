<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;

class Form extends Component
{
    public $action = null;

    public $method = 'POST';

    public $_method = 'POST';

    public function __construct(
        $action = null,
        $method = 'POST'
    ) {
        $method = strtoupper($method);
        //
        $this->action = $action;
        $this->method = ($method === 'GET' ? 'GET' : 'POST');
        $this->_method = (in_array($method, ['GET', 'POST']) ? null : $method);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.form');
    }
}
