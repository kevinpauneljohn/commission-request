<?php

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AssignedUserOnly;
use App\Http\Middleware\RequesterAllowedOnly;
use App\Http\Requests\UpdateCommissionRequest;
use App\Http\Requests\UserReqRequest;
use App\Models\User;
use App\Services\CommissionVoucherService;
use App\Services\FindingService;
use App\Services\RequestService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class RequestController extends Controller
{

    public function __construct()
    {
        $this->middleware(['permission:view request'])->only(['index','request_list','show','getParentRequest']);
        $this->middleware(['permission:add request'])->only(['store']);
        $this->middleware(['permission:edit request'])->only(['update','declineRequest']);
        $this->middleware(RequesterAllowedOnly::class)->only(['show']);
        $this->middleware(['permission:update request status'])->only(['approveRequest']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $salesDirectors = User::whereHas("roles", function($q){ $q->where("name","=","sales director"); })->get();
        return view('dashboard.commissions.index',compact('salesDirectors'));
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
    public function store(UserReqRequest $request, RequestService $requestService)
    {
        return $requestService->createRequest($request->all()) ?
            response()->json(['success' => true, 'message' => 'Request successfully created!', 'request_id' => $requestService->request_id]):
            response()->json(['success' => false, 'message' => 'An error occurred!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, RequestService $requestService)
    {
//        $parent = $requestService->parent_id($id);
////        return $requestService->get_related_request($parent);
//        return 'total released = '.$requestService->total_percentage_released($parent)
//            .' remaining balance = '.$requestService->remaining_percentage($id);
        $requestDetail = \App\Models\Request::findOrFail($id);
        $assignee = User::whereHas("roles", function($q){ $q->where("name","!=","super admin")->where("name","!=","sales director"); })->get();
        $down_lines = $requestService->all_down_lines($id);
        return view('dashboard.commissions.show',compact(
            'requestDetail','assignee', 'down_lines',
        ));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommissionRequest $request, string $id, RequestService $requestService)
    {
        $commissionRequest = \App\Models\Request::findOrFail($id);
        $commissionRequest->total_contract_price = $request->total_contract_price;
        $commissionRequest->financing = $request->financing;
        $commissionRequest->sd_rate = $request->sd_rate;

        if($commissionRequest->isDirty())
        {
            $commissionRequest->save();
            $log = 'The request details was updated';
            $requestService->requestLogsActivities($commissionRequest->id, $log, $commissionRequest, true);
            return response()->json(['success' => true,'message' => 'Request successfully updated!',
                'data' => collect($commissionRequest)->only(['total_contract_price','sd_rate'])]);
        }
        return response()->json(['success' => false,'message' => 'No changes made!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function request_list(RequestService $requestService, Request $request)
    {
        $display_request = $request->session()->get('request_display');
        if(auth()->user()->hasRole('sales director'))
        {
            if(is_null($display_request))
            {
                $request = User::findOrFail(auth()->user()->id)->requests;
            }else{
                $request = User::findOrFail(auth()->user()->id)->requests()->where('status',$display_request)->get();
            }

        }
        elseif (!auth()->user()->hasAnyRole('sales director'))
        {
            if(is_null($display_request))
            {
                $request = \App\Models\Request::all();
            }else{
                $request = \App\Models\Request::where('status',$display_request);
            }

        }
        return $requestService->requestList($request);
    }

    public function requestActivities($requestId, RequestService $requestService): \Illuminate\Http\JsonResponse
    {
        return $requestService->activities($requestId);
    }

    public function getParentRequest($request): \App\Models\Request
    {
        return \App\Models\Request::findOrFail($request);
    }

    public function declineRequest($request_id, FindingService $findingService): \Illuminate\Http\JsonResponse
    {
        return $findingService->set_request_to_declined($request_id) ?
            response()->json(['success' => true, 'message' => 'Request Declined!']) :
            response()->json(['success' => false, 'message' => 'An error occurred!']) ;
    }

    public function approveRequest(\App\Models\Request $request, CommissionVoucherService $commissionVoucherService): \Illuminate\Http\JsonResponse
    {
        return $commissionVoucherService->request_status_delivered($request) ?
            response()->json(['success' => true, 'message' => 'Status set to delivered']):
            response()->json(['success' => false, 'message' => 'An error occurred']);
    }

    public function completeRequest(\App\Models\Request $request, CommissionVoucherService $commissionVoucherService): \Illuminate\Http\JsonResponse
    {
        return $commissionVoucherService->request_status_completed($request) ?
            response()->json(['success' => true, 'message' => 'Request completed']):
            response()->json(['success' => false, 'message' => 'An error occurred']);
    }

    public function setRequestToDisplay(Request $request): void
    {
        if(is_null($request->display))
        {
            $request->session()->forget('request_display');
        }else{
            $request->session()->put('request_display',$request->display);
        }
    }
}
