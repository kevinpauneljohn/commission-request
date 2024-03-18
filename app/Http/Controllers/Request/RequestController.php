<?php

namespace App\Http\Controllers\Request;

use App\Http\Controllers\Controller;
use App\Http\Middleware\AssignedUserOnly;
use App\Http\Middleware\RequesterAllowedOnly;
use App\Http\Requests\UpdateCommissionRequest;
use App\Http\Requests\UserReqRequest;
use App\Models\User;
use App\Services\RequestService;
use Illuminate\Http\Request;

class RequestController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view request'])->only(['index','request_list','show']);
        $this->middleware(['permission:add request'])->only(['store']);
        $this->middleware(RequesterAllowedOnly::class)->only(['show']);
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
            response()->json(['success' => true, 'message' => 'Request successfully created!']):
            response()->json(['success' => false, 'message' => 'An error occurred!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id, RequestService $requestService)
    {
        $requestDetail = \App\Models\Request::findOrFail($id);
        $assignee = User::whereHas("roles", function($q){ $q->where("name","!=","super admin")->where("name","!=","sales director"); })->get();

//        $id = str_pad($requestDetail->id, 5, '0', STR_PAD_LEFT);
        $id = $requestService->requestIdFormatter($requestDetail->request_type, $requestDetail->id);
        return view('dashboard.commissions.show',compact(
            'requestDetail','id','assignee'
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
            return response()->json(['success' => true,'message' => 'Request successfully updated!']);
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

    public function request_list(RequestService $requestService)
    {
        if(auth()->user()->hasRole('sales director'))
        {
            $request = User::findOrFail(auth()->user()->id)->requests;
        }
        elseif (!auth()->user()->hasAnyRole('sales director'))
        {
            $request = \App\Models\Request::all();
        }
        return $requestService->requestList($request);
    }

    public function requestActivities($requestId, RequestService $requestService)
    {
        return $requestService->activities($requestId);
    }
}
