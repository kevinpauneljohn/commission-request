<?php

namespace App\Services;

use App\Models\Task;
use Carbon\Carbon;
use Spatie\Activitylog\Contracts\Activity;
use Yajra\DataTables\DataTables;

class TaskService
{
    public function createTask(array $task): bool
    {
        if($task = Task::create([
            'title' => $task['title'],
            'description' => $task['description'],
            'assigned_to' => $task['assign_to'],
            'creator' => auth()->user()->id,
            'status' => 'pending',
            'due_date' => Carbon::parse($task['due_date'])->format('Y-m-d'),
            'request_id' => $task['request_id']
        ]))
        {
            $log = 'Task #'.str_pad($task->id, 5, '0', STR_PAD_LEFT).' was created';
            $properties = ['task_id' => $task->id,'task_title' => $task->title];
            $this->taskLogsActivities($task, $task['request_id'], $log, $properties, true);
            return true;
        }
        return false;
    }

    public function updateTaskStatus($taskId, $status): bool
    {
        $task = Task::findOrFail($taskId);
        if($status == "completed")
        {
            if($task->actionTakens->count() == 0)
            {
                return false;
            }
        }

        $task->status = $status;
        $task->save();

        $logs = 'Task #'.str_pad($task->id, 5, '0', STR_PAD_LEFT).' updated to '.$this->status($task->status).' status';
        $properties = ['task_title' => $task->title];
        $this->taskLogsActivities($task, $task->request_id, $logs,$properties, true);
        return true;
    }

    public function taskLogsActivities($task, $request_id, $log, $properties, $display): ?Activity
    {
        return activity()
            ->causedBy(auth()->user()->id)
            ->withProperties($properties)
            ->tap(function(Activity $activity) use ($task, $request_id, $display){
                $activity->task_id = $task->id;
                $activity->display = $display;
                $activity->request_id = $request_id;
            })
            ->log($log);
    }

    public function taskList($tasks): \Illuminate\Http\JsonResponse
    {
        return DataTables::of($tasks)
            ->editColumn('assigned_to',function($task){
                return '<span class="text-fuchsia">'.ucwords($task->assignedTo->full_name).'</span>';
            })
            ->editColumn('creator',function($task){
                return '<span class="text-green">'.ucwords($task->author->full_name).'</span>';
            })
            ->editColumn('status',function($task){
                return $this->status($task->status);
            })
            ->editColumn('created_at',function($task){
                return $task->created_at->format('M d, Y g:i:s a');
            })
            ->editColumn('id', function($task){
                $id = str_pad($task->id, 5, '0', STR_PAD_LEFT);
                return '<a href="'.route('task.show',['task' => $task->id]).'"><span style="color:#007bff">'.$id.'</span></a>';
            })
            ->addColumn('action_taken',function($task){
                return $task->actionTakens->count();
            })
            ->addColumn('action',function($task){
                $action = "";

                if(auth()->user()->can('view task'))
                {
                    $action .= '<a href="'.route('task.show',['task' => $task->id]).'" class="btn btn-xs btn-success view-task-btn" id="'.$task->id.'">View</a>';

                }
                return $action;
            })
            ->rawColumns(['action','id','assigned_to','creator','status'])
            ->make(true);
    }

    private function status($status)
    {
        switch ($status){
            case $status == "pending";
                return'<span class="badge badge-warning">'.$status.'</span>';
            case $status == "on-going";
                return '<span class="badge badge-primary">'.$status.'</span>';
            case $status == "completed";
                return '<span class="badge badge-success">'.$status.'</span>';
            default:
                return "";
        }
    }
    public function actionTakens($actionTakens)
    {
        return DataTables::of($actionTakens)
            ->editColumn('created_at',function($actionTaken){
                return $actionTaken->created_at->format('M d, Y g:i:s a');
            })
            ->addColumn('action_taken',function($actionTaken){
                return $actionTaken->action;
            })
            ->editColumn('user_id',function($actionTaken){
                return $actionTaken->user->full_name;
            })
            ->addColumn('action',function($actionTaken){
                $action = "";
                $user = auth()->user();
                if($user->can('edit action taken') && $actionTaken->task->assigned_to == $user->id  && $actionTaken->task->status != "completed" || $user->hasRole('super admin'))
                {
                    $action .= '<button class="btn btn-xs btn-primary edit-action-taken-btn mr-1" id="'.$actionTaken->id.'">Edit</button>';

                }
                if($user->can('delete action taken') && $actionTaken->task->assigned_to == $user->id  && $actionTaken->task->status != "completed" || $user->hasRole('super admin'))
                {
                    $action .= '<button class="btn btn-xs btn-danger delete-action-taken-btn" id="'.$actionTaken->id.'">Delete</button>';

                }
                return $action;
            })
            ->rawColumns(['action','action_taken'])
            ->make(true);
    }
}
