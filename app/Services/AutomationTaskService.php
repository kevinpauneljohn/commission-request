<?php

namespace App\Services;

use App\Models\AutomationTask;
use Illuminate\Support\Facades\DB;
use Yajra\DataTables\DataTables;

class AutomationTaskService
{
    public function create($request)
    {
        return (bool)AutomationTask::create([
            'automation_id' => $request->automation_id,
            'title' => $request->title,
            'description' => $request->description,
            'assigned_to_role' => $request->assign_to,
            'creator' => auth()->user()->id,
            'days_before_due_date' => $request->days_before_due_date
        ]);
    }

    public function changeSequence($automation_id, $task_id, $sequence_number)
    {
        if(!$this->sequence_number_is_taken($automation_id, $sequence_number) || $sequence_number == 0)
        {
            DB::table('automation_tasks')
                ->where('automation_id',$automation_id)
                ->where('id',$task_id)->update(['sequence_id' => $sequence_number]);
            return true;
        }
        return false;
    }

    private function sequence_number_is_taken($automation_id, $sequence_number): bool
    {
        return AutomationTask::where('automation_id',$automation_id)->where('sequence_id',$sequence_number)->count() > 0;
    }

    public function list($query)
    {
        $number_of_task = $query->count();
        return DataTables::of($query->get())
            ->addColumn('sequence_number',function ($taskTemplate){
                return $taskTemplate->sequence_id;
            })
            ->editColumn('creator',function($taskTemplate){
                return $taskTemplate->author->full_name;
            })
            ->editColumn('sequence_id',function($taskTemplate) use ($number_of_task){
                $sequence = is_null($taskTemplate->sequence_id) ? 0 : $taskTemplate->sequence_id;
                $option = '<select class="form-control sequence" id="' . $taskTemplate->id . '">';
                $option .= '<option value="0">0</option>';
                    for ($ctr = 1; $ctr <= $number_of_task; $ctr++){
                        $selected = $ctr == $taskTemplate->sequence_id ? 'selected' :'';
                        $disabled = AutomationTask::where('sequence_id',$ctr)->count() > 0 ? 'class="bg-gray" disabled' :'';
                        $option .= '<option value="'.$ctr.'" '.$selected.' '.$disabled.'>'.$ctr.'</option>';
                    }
                $option .= "</select>";
                return $option;
            })
            ->addColumn('action',function($taskTemplate){
                $action = "";
                $user = auth()->user();

                if($user->can('edit automation'))
                {
                    $action .= '<button class="btn btn-xs btn-primary edit-automation-btn mr-1" id="'.$taskTemplate->id.'">Edit</button>';

                }
                if($user->can('delete automation'))
                {
                    $action .= '<button class="btn btn-xs btn-danger delete-automation-btn" id="'.$taskTemplate->id.'">Delete</button>';

                }
                return $action;
            })
            ->rawColumns(['action','description','sequence_id'])
            ->make(true);
    }
}
