<?php

namespace App\View\Components\Task;

use App\Services\RequestService;
use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class Task extends Component
{
    public $assignee;
    public $createButton;
    public $remaining_balance;

    /**
     * Create a new component instance.
     */
    public function __construct(RequestService $requestService, $assignee, public $requestId = null, $createButton = false)
    {
        $this->assignee = $assignee;
        $this->createButton = $createButton;
        $this->remaining_balance = $requestService->remaining_percentage($this->requestId);
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.task.task');
    }
}
