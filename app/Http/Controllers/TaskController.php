<?php

namespace App\Http\Controllers;

use App\Http\Middleware\AssignedUserOnly;
use App\Http\Middleware\TaskAssignedToOnly;
use App\Models\Request;
use App\Models\Task;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Services\RequestService;
use App\Services\TaskService;

class TaskController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view task'])->only(['tasks','getTaskByRequestId','taskActionTakens']);
        $this->middleware(['permission:update task status'])->only(['updateStatus']);
        $this->middleware([TaskAssignedToOnly::class])->only(['updateStatus']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.tasks.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request, TaskService $taskService)
    {
        return $taskService->createTask($request->all()) ?
            response()->json(['success' => true, 'message' => 'Task successfully created!']) :
            response()->json(['success' => false, 'message' => 'An error occurred!']) ;
    }

    /**
     * Display the specified resource.
     */
    public function show(Task $task)
    {
        return view('dashboard.tasks.show',compact('task'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Task $task)
    {
        return $task;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, Task $task, TaskService $taskService)
    {
        return $taskService->updateTask($request->all(), $task) ?
            response()->json(['success' => true, 'message' => 'Task successfully updated!']):
            response()->json(['success' => false, 'message' => 'No changes made!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Task $task)
    {
        if($task->actionTakens->count() == 0)
        {
            $task->delete();
            return response()->json(['success' => true, 'message' => 'Task successfully deleted!']);
        }
        return response()->json(['success' => false, 'message' => 'A task cannot be deleted because there has already been action taken!']);
    }

    public function tasks(TaskService $taskService)
    {
        return $taskService->taskList(Task::all());
    }

    public function getTaskByRequestId(TaskService $taskService, $requestId): \Illuminate\Http\JsonResponse
    {
        return $taskService->taskList(Request::findOrFail($requestId)->tasks);
    }

    public function updateStatus(\Illuminate\Http\Request $request, TaskService $taskService): \Illuminate\Http\JsonResponse
    {
        return $taskService->updateTaskStatus($request->task, $request->status) ?
            response()->json(['success' => true,'message' => 'Status successfully updated']) :
            response()->json(['success' => false, 'message' => 'Add action taken first!']) ;
    }

    public function taskActionTakens(Task $task, TaskService $taskService): \Illuminate\Http\JsonResponse
    {
        return $taskService->actionTakens($task->actionTakens);
    }

    public function createNextTaskFromTemplate(Task $task, RequestService $requestService): \Illuminate\Http\JsonResponse
    {
        return $requestService->get_next_task($task->request_id, $task->id, $requestService) ?
            response()->json(['success' => true, 'message' => 'New task successfully added!']) :
            response()->json(['success' => false, 'message' => 'Add action taken first!']) ;
    }

}
