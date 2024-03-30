<?php

namespace App\View\Components\Request;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Vouchers extends Component
{
    public int|null $requestId;
    /**
     * Create a new component instance.
     */
    public function __construct($requestId)
    {
        $this->requestId = $requestId;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.request.vouchers');
    }
}
