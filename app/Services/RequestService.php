<?php

namespace App\Services;

use App\Mail\RequestCreated;
use App\Models\Automation;
use App\Models\CommissionVoucher;
use App\Models\Request;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
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
                'assigned_to' => $this->assign_to($taskTemplate->assigned_to_role),
                'creator' => $taskTemplate->creator,
                'status' => 'pending',
                'due_date' => now()->addDays(intval($taskTemplate->days_before_due_date))->format('Y-m-d'),
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
        $requests = Request::where('parent_request_id',$request_id);
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
        $total_task_template = $this->total_template_tasks();
        return DataTables::of($userRequest)
            ->editColumn('id', function($request){
                return '<a href="'.route('request.show',['request' => $request->id]).'"><span style="color:#007bff">'.$request->formatted_id.'</span></a>';
            })
            ->editColumn('created_at', function($request){
                return $request->created_at->format('M d, Y g:i:s a');
            })
            ->editColumn('project', function($request){
                return ucwords(strtolower($request->project));
            })
            ->editColumn('buyer', function($request){
                $fullName ='';
                $fullName .= ucwords(strtolower($request->buyer->firstname));
                if($request->buyer->middlename != "")
                {
                    $fullName .=' '.ucwords(strtolower($request->buyer->middlename));
                }
                $fullName .= ' '.ucwords(strtolower($request->buyer->lastname));
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

                return '<span class="text-pink">'.number_format($request->total_contract_price,2).'</span>';
            })
            ->editColumn('sd_rate', function($request){

                return '<span class="text-primary text-bold">'.$request->sd_rate.'%</span>';
            })
            ->editColumn('payment_type', function($request){

                return collect($request->commissionVoucher)->count() > 0 ?
                    '<span class="text-purple">'.$request->commissionVoucher->payment_type.'</span>' : '';
            })
            ->editColumn('financial_service', function($request){

                return collect($request->commissionVoucher)->count() > 0 ?
                    '<span>'.$request->commissionVoucher->issuer.'</span>' : '';
            })
            ->editColumn('cheque_amount', function($request){

                return collect($request->commissionVoucher)->count() > 0 ?
                    '<span class="text-success text-bold">'.number_format($request->commissionVoucher->amount_transferred,2).'</span>' : '';
            })
            ->editColumn('user_id', function($request){

                return ucwords($request->user->firstname.' '.$request->user->lastname);
            })
            ->addColumn('progress',function($request) use ($total_task_template){
                if($total_task_template !== 0)
                {
                    $request_completed_task = $request->tasks()->where('status','completed')->count();
                    $progress = ($request_completed_task / $total_task_template) * 100;

                    if($request->status == "completed")
                    {
                        $progress = 100;
                    }
                    if($request->status == "declined")
                    {
                        $progress = 100;
                    }

                    if ($progress >= 26 && $progress <= 50)
                    {
                        $backgroundColor = 'bg-warning';
                    }
                    elseif ($progress >= 51 && $progress <= 99)
                    {
                        $backgroundColor = 'bg-primary';
                    }
                    elseif ($progress == 100)
                    {
                        $backgroundColor = 'bg-success';
                    }
                    else{
                        $backgroundColor = 'bg-danger';
                    }

                    return '<div class="progress progress-md">
                          <div class="progress-bar progress-bar-striped progress-bar-animated '.$backgroundColor.'" style="width: '.$progress.'%">'.$progress.'%</div>
                        </div>';
                }

            })
            ->editColumn('parent_request',function($request){
                if(!is_null($request->parent_request_id))
                {
                    return '<a href="'.route('request.show',['request' => $request->parent_request_id]).'"><span style="color:#007bff">'.$request->parent_request.'</span></a>';
                }

                return '';
            })
            ->addColumn('percent_released', function($request){
                return collect($request->commissionVoucher)->count() > 0 ?
                    '<span class="text-primary text-bold">'.$request->commissionVoucher->voucher->percentage_released.'%</span>' : 0 .'%';
            })
            ->addColumn('total_released', function($request){
                if(collect($this->get_related_request($request->id))->count() > 0)
                {
                    return $this->get_related_request($request->id)[0] == $request->id ? '<span class="text-purple text-bold">'.$this->total_percentage_released($request->id).'%</span>' : '';
                }
                return '';
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
                    $action .= '<a href="'.route('request.show',['request' => $request->id]).'" class="btn btn-xs btn-success request-btn mr-1 mb-1" id="'.$request->id.'">'.$text.'</a>';

                }
                if(auth()->user()->can('add request') && collect($request->commissionVoucher)->count() > 0 && $request->status == 'completed')
                {
                    if($this->total_percentage_released($request->id) < 100 && $this->display_request_remaining_button($request->id))
                    {
                        $action .= '<a href="'.route('request.index').'?parent_request='.$request->id.'&remaining=true" class="btn btn-xs bg-purple request-btn mr-1 mb-1" id="'.$request->id.'">Request Remaining</a>';
                    }
                }
                return $action;
            })
            ->setRowClass(function ($request) {
                if(auth()->user()->can('add request') && collect($request->commissionVoucher)->count() > 0 && $request->status == 'completed')
                {
                    if($this->total_percentage_released($request->id) < 100 && $this->display_request_remaining_button($request->id))
                    {
                        return $this->get_related_request($request->id)[0] == $request->id ? 'with-remaining' : '';
                    }
                }
//                if(collect($this->get_related_request($request->id))->count() > 0)
//                {
//                    return $this->get_related_request($request->id)[0] == $request->id ? 'with-remaining' : '';
//                }
                return '';
            })
            ->rawColumns(['total_released','action','sd_rate','payment_type','financial_service','cheque_amount','id','total_contract_price','status','parent_request','progress','percent_released'])
            ->with([
                'total_completed_released' => $this->total_commission_released()
            ])
            ->make(true);
    }

    public function total_commission_released()
    {
        return CommissionVoucher::whereIn('request_id',$this->completed_request_ids())->sum('amount_transferred');
    }

    private function completed_request_ids()
    {
        return collect(Request::where('status','completed')->orWhere('status','delivered')->get())->pluck('id');
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


    private function display_request_remaining_button($request_id): bool
    {
        return $this->get_related_request($request_id)[0] == $request_id;
    }

    public function total_percentage_released($request_id)
    {
        $requests = Request::whereIn('id',$this->get_related_request($request_id))->get();
        $sum = 0;
        foreach ($requests as $request)
        {
            if(collect($request->commissionVoucher)->count() > 0)
            {
                $sum = $sum + $request->commissionVoucher->voucher->percentage_released;
            }
        }
        return $sum;
    }

    public function remaining_percentage($request_id)
    {
        if(Request::find($request_id)->status != 'declined')
        {
            $parent = $this->parent_id($request_id);
            return 100 - $this->total_percentage_released($parent);
        }
        return 0;
    }

    public function get_related_request($request_id): ?array
    {
        if(!$this->is_request_declined($request_id))
        {
            $request = Request::where('id',$request_id)->where('status','!=','declined');

            $child = collect($request->orWhere('parent_request_id',$request_id)->where('status','completed')->get())->pluck('id')->toArray();
            $parent_id = $request->first()->parent_request_id;
            $related_request = $child;
            if($this->is_parent_completed($parent_id))
            {
                $related_request = collect($child)->merge((array)$parent_id)->toArray();
                sort($related_request);
            }
            return $related_request;
        }
        return null;
    }

    public function parent_id($request_id)
    {
        $current = Request::find($request_id);
        if(!$this->is_request_declined($current->id))
        {
            if(!is_null($current->parent_request_id))
            {
                if(!$this->is_request_declined($current->parent_request_id))
                {
                    return $current->parent_request_id;
                }else{
                    return $current->id;
                }
            }else{
                return $current->id;
            }
        }
        return $current->id;
    }


    private function is_request_declined($request_id): bool
    {
        return Request::find($request_id)->status == 'declined';
    }

    private function is_parent_completed($parent_request_id): bool
    {
        if(!is_null($parent_request_id))
        {
            return Request::find($parent_request_id)->status == 'completed';
        }
        return false;
    }

    private function total_template_tasks()
    {
        return collect($this->automation())->count() > 0 ? $this->automation()->automationTasks->count() : 0;
    }


}
