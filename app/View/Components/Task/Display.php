<?php

namespace App\View\Components\Task;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\View\Component;

class Display extends Component
{
    public mixed $display_task;
    /**
     * Create a new component instance.
     */
    public function __construct(Request $request)
    {
        $this->display_task = $request->session()->get('task');
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.task.display');
    }
}
