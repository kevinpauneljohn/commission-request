<?php

namespace App\View\Components\Task;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class TableList extends Component
{
    public $assignee;
    public $requestId;
    public $createButton;
    /**
     * Create a new component instance.
     */
    public function __construct($assignee, $requestId, $createButton = false)
    {
        $this->assignee = $assignee;
        $this->requestId = $requestId;
        $this->createButton = $createButton;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.task.table-list');
    }
}
