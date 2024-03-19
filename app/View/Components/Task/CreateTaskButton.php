<?php

namespace App\View\Components\Task;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class CreateTaskButton extends Component
{
    public $createButton;
    /**
     * Create a new component instance.
     */
    public function __construct($createButton = false)
    {
        $this->createButton = $createButton;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.task.create-task-button');
    }
}
