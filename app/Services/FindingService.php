<?php

namespace App\Services;

use App\Models\Finding;
use App\Models\Request;
use App\Models\Task;
use Spatie\Activitylog\Contracts\Activity;
use Yajra\DataTables\DataTables;

class FindingService
{
    public ActionTakenService $actionTakenService;
    public function __construct(ActionTakenService $actionTakenService)
    {
        $this->actionTakenService = $actionTakenService;
    }

    public function createFinding(array $finding): bool
    {
        if ($findings = Finding::create([
            'request_id' => $finding['request_id'],
            'findings' => nl2br($finding['findings']),
            'user_id' => auth()->user()->id
        ]))
        {
            $log = 'There is a finding in your request. See the details here <a href="'.route('finding.show',['finding' => $findings->id]).'" class="text-info">Click to view details</a>';
            $this->findingsLogsActivities($findings->request_id, $log, $findings, true);
            return true;
        }
        return false;
    }

    public function create_findings_from_task($request): bool
    {
        $task = Task::find($request->task_id);
        if($findings = Finding::create([
            'request_id' => $task->request_id,
            'findings' => $request->description,
            'user_id' => auth()->user()->id,
        ]))
        {
            $this->set_request_to_on_going($task->request_id);
            $this->set_task_status_to_completed($request);

            $log = "You're request has been declined. ";
            $log .= 'See the findings here <a href="'.route('finding.show',['finding' => $findings->id]).'" class="text-info">Click to view details</a>';
            $this->findingsLogsActivities($findings->request_id, $log, $findings, true);
            return true;
        }
        return false;
    }

    private function set_task_status_to_completed($request): void
    {
        if($this->actionTakenService->create($request))
        {
            $task = Task::find($request->task_id);
            $task->status = 'completed';
            $task->save();
        }
    }

    public function set_request_to_declined($request_id): bool
    {
        $request = Request::find($request_id);
        $request->status = 'declined';
        return (bool)$request->save();
    }

    public function set_request_to_on_going($request_id): void
    {
        $request = Request::find($request_id);
        $request->status = 'on-going';
        $request->save();
    }

    public function findingsLogsActivities($request_id, $log, $properties, $display): ?Activity
    {
        return activity()
            ->causedBy(auth()->user()->id)
            ->withProperties($properties)
            ->tap(function(Activity $activity) use ($request_id, $display){
                $activity->display = $display;
                $activity->request_id = $request_id;
            })
            ->log($log);
    }

    public function findingsList($findings)
    {
        return DataTables::of($findings)
            ->addIndexColumn()
            ->editColumn('created_at', function($finding){
                return $finding->created_at->format('M d, Y g:i:s a');
            })
            ->editColumn('user_id', function($finding){
                return $finding->user->full_name;
            })
            ->addColumn('action',function($finding){
                $action = "";

                if(auth()->user()->can('view task'))
                {
                    $action .= '<button class="btn btn-xs btn-success view-finding-btn" id="'.$finding->id.'">View</button>';

                }
                return $action;
            })
            ->rawColumns(['action','findings'])
            ->make(true);
    }
}
