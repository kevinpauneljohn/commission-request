<?php

namespace App\View\Components\Request;

use App\Models\Request;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class InfoBox extends Component
{
    public $declined;
    public $pending;
    public $on_going;
    public $completed;
    /**
     * Create a new component instance.
     */
    public function __construct()
    {
        $this->declined = Request::where('status','declined')->count();
        $this->pending = Request::where('status','pending')->count();
        $this->on_going = Request::where('status','on-going')->count();
        $this->completed = Request::where('status','completed')->count();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.request.info-box');
    }
}
