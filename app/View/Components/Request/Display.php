<?php

namespace App\View\Components\Request;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class Display extends Component
{
    public mixed $display_request;
    /**
     * Create a new component instance.
     */
    public function __construct(Request $request)
    {
        $this->display_request = $request->session()->get('request_display');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.request.display');
    }
}
