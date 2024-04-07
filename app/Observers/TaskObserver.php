<?php

namespace App\Observers;

use App\Mail\TaskCreated;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Support\Facades\Mail;

class TaskObserver
{
    public TaskService $taskService;

    public function __construct(TaskService $taskService)
    {
        $this->taskService = $taskService;
    }

    /**
     * Handle the Task "created" event.
     */
    public function created(Task $task): void
    {
        Mail::to($task->assignedTo->email)->bcc(['johnkevinpaunel@gmail.com'])->send(new TaskCreated($task));
    }

    /**
     * Handle the Task "updated" event.
     */
    public function updated(Task $task): void
    {

    }

    /**
     * Handle the Task "deleted" event.
     */
    public function deleted(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "restored" event.
     */
    public function restored(Task $task): void
    {
        //
    }

    /**
     * Handle the Task "force deleted" event.
     */
    public function forceDeleted(Task $task): void
    {
        //
    }
}
