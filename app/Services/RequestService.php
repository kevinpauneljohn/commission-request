<?php

namespace App\Services;

use App\Models\Automation;
use App\Models\Request;
use App\Models\Task;
use App\Models\User;
use Spatie\Activitylog\Models\Activity;
use Yajra\DataTables\Facades\DataTables;

class RequestService extends \App\Services\TaskService
{
    public $child_request;
    public $child_request_id;
    public $request_id;

    public function createRequest(array $requestData): bool
    {
        if($request =Request::create([
            'buyer' => [
                'firstname' => $requestData['firstname'],
                'middlename' => $requestData['middlename'],
                'lastname' => $requestData['lastname'],
            ],
            'project' => $requestData['project'],
            'model_unit' => $requestData['model_unit'],
            'phase' => $requestData['phase'],
            'block' => $requestData['block'],
            'lot' => $requestData['lot'],
            'total_contract_price' => $requestData['total_contract_price'],
            'financing' => $requestData['financing'],
            'request_type' => $requestData['request_type'],
            'sd_rate' => $requestData['sd_rate'],
            'message' => $requestData['message'],
            'user_id' => !auth()->user()->hasRole('sales director') ? $requestData['sales_director'] : auth()->user()->id,
            'parent_request_id' => collect($requestData)->has('parent_request_id')  ?
                    $this->is_request_id_exists($requestData['parent_request_id']) ? $requestData['parent_request_id']: null
                : null,
            'status' => 'pending',
        ]))
        {
            $this->request_id = $request->id;
            return true;
        }
        return false;
    }

    private function is_request_id_exists($request_id)
    {
        return Request::where('id',$request_id)->count() > 0;
    }

    private function task($request_id)
    {
        return Task::where('request_id',$request_id);
    }

    private function automation()
    {
        return Automation::where('is_active',true)->first();
    }

    /**
     * this will return the next automation task id to be used
     * @param $automation
     * @param $request_id
     * @return mixed
     */
    private function task_template($automation, $request_id): mixed
    {
        if($this->is_request_exists_in_tasks($request_id) && $this->is_request_task_template_used($request_id, $automation->id))
        {
            return $automation->automationTasks()
                ->whereNotin('id',$this->excluded_task_template_id($request_id, $automation->id))
                ->orderBy('sequence_id','asc')->first();
        }
        return $automation->automationTasks()
            ->orderBy('sequence_id','asc')->first();
    }
    /**
     * this will check if there are tasks already in the request
     * @param $request_id
     * @return bool
     */
    private function is_request_exists_in_tasks($request_id): bool
    {
        return $this->task($request_id)->count() > 0;
    }

    /**
     * this will check if the task saved came from the automated task templates
     * @param $request_id
     * @param $automation_id
     * @return bool
     */
    private function is_request_task_template_used($request_id, $automation_id): bool
    {
        return $this->task($request_id)->where('automation_id',$automation_id)->count() > 0;
    }

    private function get_task_template_id_used($request_id, $automation_id): \Illuminate\Support\Collection
    {
        return collect($this->task($request_id)->where('automation_id', $automation_id)->get())
            ->pluck('automation_task_id');
    }

    private function excluded_task_template_id($request_id, $automation_id): \Illuminate\Support\Collection
    {
        return $this->get_task_template_id_used($request_id, $automation_id);
    }

    private function get_automation_count()
    {
        return Automation::where('is_active',true)->count();
    }

    private function get_automation_task_count()
    {
        return $this->automation()->automationTasks->count();
    }

    private function is_task_template_the_end($task_template_id): bool
    {
        $last_automated_task = collect($this->automation()->automationTasks()->orderBy('sequence_id','asc')->get())->last();
        return  $last_automated_task->id == $task_template_id;
    }

    public function assign_to($role)
    {
        $users = User::whereHas("roles", function($q) use ($role){ $q->where("name","=",$role); })->get();
        $data = array();
        foreach ($users as $user){
//            $data['task-'.intval($user->tasks()->where('status','pending')->orWhere('status','on-going')->count())] = $user->id;
            $data['task-'.intval($user->tasks->count())] = $user->id;
        }
        ksort($data);
        return collect($data)->first();
    }

    public function generate_automated_task($request_id): void
    {
        $automation = $this->automation();
        if($this->get_automation_count() > 0 && $this->get_automation_task_count() > 0)
        {
            $taskTemplate = $this->task_template($automation, $request_id);
            Task::create([
                'title' => $taskTemplate->title,
                'description' => $taskTemplate->description,
                'assigned_to' => $this->assign_to($taskTemplate->assigned_to_role), //create a function that will assign to a designated user
                'creator' => $taskTemplate->creator,
                'status' => 'pending',
                'due_date' => now()->format('Y-m-d'),
                'request_id' => $request_id,
                'automation_id' => $automation->id,
                'automation_task_id' => $taskTemplate->id,
                'is_end' => $this->is_task_template_the_end($taskTemplate->id)
            ]);
        }

    }


    public function get_next_task($request_id, $task_id, TaskService $taskService): bool
    {
//        check if an action taken was created first
        if($this->is_task_have_action_taken($task_id))
        {
            $taskService->updateTaskStatus($task_id, 'completed');
            $this->generate_automated_task($request_id);
            return true;
        }
        return false;
    }

    private function is_task_have_action_taken($task_id): bool
    {
        return Task::findOrFail($task_id)->actionTakens->count() > 0;
    }

    public function all_down_lines($request_id): array
    {
        $child = [];
        for ($ctr = 0; $this->requestDownLines($request_id); $ctr++){
            $child[$ctr] = $this->child_request;
            $request_id = $this->child_request_id;
        }
        return $child;
    }

    public function requestDownLines($request_id): bool
    {
        $requests = \App\Models\Request::where('parent_request_id',$request_id);
        if($requests->count() > 0)
        {
            $this->child_request = $requests->first();
            $this->child_request_id = $requests->first()->id;
            return true;
        }

        return false;
    }


    public function requestLogsActivities($request_id, $log, $properties, $display): ?\Spatie\Activitylog\Contracts\Activity
    {
        return \activity()
            ->causedBy(auth()->user()->id)
            ->withProperties($properties)
            ->tap(function(\Spatie\Activitylog\Contracts\Activity $activity) use ($request_id, $display){
                $activity->display = $display;
                $activity->request_id = $request_id;
            })
            ->log($log);
    }

    public function requestList($userRequest)
    {
        return DataTables::of($userRequest)
            ->editColumn('id', function($request){
                return '<a href="'.route('request.show',['request' => $request->id]).'"><span style="color:#007bff">'.$request->formatted_id.'</span></a>';
            })
            ->editColumn('created_at', function($request){
                return $request->created_at->format('M d, Y g:i:s a');
            })
            ->editColumn('buyer', function($request){
                $fullName ='';
                $fullName .= $request->buyer->firstname;
                if($request->buyer->middlename != "")
                {
                    $fullName .=' '.$request->buyer->middlename;
                }
                $fullName .= ' '.$request->buyer->lastname;
                return $fullName;
            })
            ->addColumn('phase_block_lot', function($request){
                $location = '';
                if(!is_null($request->phase))
                {
                    $location .= 'Phase '.$request->phase.' ';
                }
                $location .= 'Block '.$request->block.' Lot '.$request->lot;
                return $location;
            })
            ->editColumn('total_contract_price', function($request){

                return '<span class="text-pink">&#8369; '.number_format($request->total_contract_price,2).'</span>';
            })
            ->editColumn('sd_rate', function($request){

                return '<span class="text-primary text-bold">'.$request->sd_rate.'%</span>';
            })
            ->editColumn('cheque_number', function($request){

                return '<span class="text-purple">'.$request->cheque_number.'</span>';
            })
            ->editColumn('cheque_amount', function($request){

                return '<span class="text-success">&#8369; '.number_format($request->cheque_amount,2).'</span>';
            })
            ->editColumn('user_id', function($request){

                return ucwords($request->user->firstname.' '.$request->user->lastname);
            })
            ->editColumn('parent_request',function($request){
                return '<span class="text-primary">'.$request->parent_request.'</span>';
            })
            ->editColumn('status',function($request){
                return $request->colored_status;
            })
            ->addColumn('action',function($request){
                $action = "";
                $text = 'Manage';
                if(auth()->user()->hasRole('sales director'))
                {
                    $text = 'View';
                }

                if(auth()->user()->can('view request'))
                {
                    $action .= '<a href="'.route('request.show',['request' => $request->id]).'" class="btn btn-xs btn-success request-btn" id="'.$request->id.'">'.$text.'</a>';

                }
                return $action;
            })
            ->rawColumns(['action','sd_rate','cheque_number','cheque_amount','id','total_contract_price','status','parent_request'])
            ->make(true);
    }

    public function activities($request_id)
    {
        return DataTables::of(Activity::where('request_id',$request_id)->where('display',true)->get())
            ->addIndexColumn()
            ->editColumn('description',function($activity){
                $causer = "";
                if(!auth()->user()->hasRole('sales director'))
                {
                    $causer ='<br/>by <span>'.User::find($activity->causer_id)->full_name.'</span>';
                }
                $taskTitle = '';
                if(collect($activity->properties)->has(['task_title']))
                {
                    $taskTitle = '<br/><span class="mb-1">'.$activity->properties['task_title'].'</span>';
                }

                return '<div class="text-bold">'.$activity->description.'</div>'.$taskTitle.'<br/><span class="text-info">'
                    .$activity->created_at->format('M d, Y g:i:s a').'</span>'.$causer;
            })
            ->rawColumns(['description'])
            ->make(true);
    }
}
