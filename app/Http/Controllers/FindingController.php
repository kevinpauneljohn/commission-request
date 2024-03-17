<?php

namespace App\Http\Controllers;

use App\Models\Finding;
use App\Http\Requests\StoreFindingRequest;
use App\Http\Requests\UpdateFindingRequest;
use App\Services\FindingService;

class FindingController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:add finding'])->only(['store']);
        $this->middleware(['permission:view finding'])->only(['findingsList']);
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
    public function store(StoreFindingRequest $request, FindingService $findingService)
    {
        return $findingService->createFinding($request->all()) ?
            response()->json(['success' => true, 'message' => 'Findings successfully added!']) :
            response()->json(['success' => false, 'message' => 'An error occurred!']);
    }

    /**
     * Display the specified resource.
     */
    public function show(Finding $finding)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Finding $finding)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateFindingRequest $request, Finding $finding)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Finding $finding)
    {
        //
    }

    public function findingsList($requestId, FindingService $findingService)
    {
        $findings = Finding::where('request_id',$requestId)->get();
        return $findingService->findingsList($findings);
    }
}
