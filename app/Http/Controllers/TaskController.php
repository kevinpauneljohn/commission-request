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
        $this->middleware(['permission:view task'])->only(['tasks','getTaskByRequestId','taskActionTakens','setTaskToDisplay','show']);
        $this->middleware(['permission:update task status'])->only(['updateStatus']);
        $this->middleware([TaskAssignedToOnly::class])->only(['updateStatus']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(\Illuminate\Http\Request $request)
    {
        $display_task = $request->session()->get('task');
        return view('dashboard.tasks.index',compact('display_task'));
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
        abort_if(is_null($task->request), 404);
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

    public function tasks(TaskService $taskService, \Illuminate\Http\Request $request)
    {
        if(!is_null($request->session()->get('task')))
        {
         $task = Task::where('assigned_to',auth()->user()->id)->where('status','pending')->get();
        }else{
            $task = Task::where('status','pending')->get();
        }
        return $taskService->taskList($task);
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

    public function setTaskToDisplay(\Illuminate\Http\Request $request): void
    {
        if(is_null($request->task))
        {
            $request->session()->forget('task');
        }else{
            $request->session()->put('task',$request->task);
        }
    }

}
