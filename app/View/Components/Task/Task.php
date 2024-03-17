<?php

namespace App\View\Components\Task;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Task extends Component
{
    public $assignee;
    public $createButton;

    /**
     * Create a new component instance.
     */
    public function __construct($assignee, public $requestId = null, $createButton = false)
    {
        $this->assignee = $assignee;
        $this->createButton = $createButton;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.task.task');
    }
}
