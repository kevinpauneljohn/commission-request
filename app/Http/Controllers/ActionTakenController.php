<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreActionTakenRequest;
use App\Models\ActionTaken;
use App\Services\ActionTakenService;
use Illuminate\Http\Request;

class ActionTakenController extends Controller
{
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
    public function store(StoreActionTakenRequest $request, ActionTakenService $actionTakenService)
    {
        return $actionTakenService->create($request)?
            response()->json(['success' => true, 'message' => 'Action taken successfully added']) :
            response()->json(['success' => false, 'message' => 'An error occurred']) ;
    }

    /**
     * Display the specified resource.
     */
    public function show(ActionTaken $actionTaken)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(ActionTaken $actionTaken)
    {
        return $actionTaken;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ActionTaken $actionTaken)
    {
        $actionTaken->action = $request->description;
        if($actionTaken->isClean())
        {
            return response()->json(['success' => false, 'message' => 'No changes made']) ;
        }
        return $actionTaken->save() ?
            response()->json(['success' => true, 'message' => 'Action taken successfully updated']) :
            response()->json(['success' => false, 'message' => 'An error occurred']) ;
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ActionTaken $actionTaken)
    {
        return $actionTaken->delete() ?
            response()->json(['success' => true, 'message' => 'Action taken successfully deleted']):
            response()->json(['success' => false, 'message' => 'An error occurred']);
    }
}
