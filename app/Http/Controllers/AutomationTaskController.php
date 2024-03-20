<?php

namespace App\Http\Controllers;

use App\Models\AutomationTask;
use App\Http\Requests\StoreAutomationTaskRequest;
use App\Http\Requests\UpdateAutomationTaskRequest;
use App\Services\AutomationTaskService;

class AutomationTaskController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:add automation'])->only(['store']);
        $this->middleware(['permission:view automation'])->only(['show','automationTaskList']);
        $this->middleware(['permission:edit automation'])->only(['updateSequence']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
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
    public function store(StoreAutomationTaskRequest $request, AutomationTaskService $automationTaskService)
    {
        return $automationTaskService->create($request) ?
            response()->json(['success' => true, 'message' => 'Automation task successfully created!']) :
            response()->json(['success' => false, 'message' => 'An error occurred!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(AutomationTask $automationTask)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(AutomationTask $automationTask)
    {
        return $automationTask;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAutomationTaskRequest $request, AutomationTask $automationTask)
    {
        $automationTask->title = $request->title;
        $automationTask->description = $request->description;
        $automationTask->assigned_to_role = $request->assign_to;
        $automationTask->days_before_due_date = $request->days_before_due_date;
        if($automationTask->isDirty())
        {
            $automationTask->save();
            return response()->json(['success' => true, 'message' => 'Task template successfully updated!']);
        }
        return response()->json(['success' => false, 'message' => 'No changes made!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(AutomationTask $automationTask)
    {
        return $automationTask->delete() ?
            response()->json(['success' => true, 'message' => 'Task template successfully deleted!']) :
            response()->json(['success' => false, 'message' => 'An error occurred!']);
    }

    public function automationTaskList($automation_id, AutomationTaskService $automationTaskService)
    {
        return $automationTaskService->list(AutomationTask::where('automation_id',$automation_id)->orderBy('sequence_id','asc'));
    }

    public function updateSequence($automation_id, $task_template_id, $sequence_number, AutomationTaskService $automationTaskService)
    {
        return $automationTaskService->changeSequence($automation_id, $task_template_id, $sequence_number)?
            response()->json(['success' => true, 'message' => 'Sequence successfully updated']):
            response()->json(['success' => false, 'message' => 'Please choose other number']);
    }
}
