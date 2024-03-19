<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Http\Requests\StoreAutomationRequest;
use App\Http\Requests\UpdateAutomationRequest;

class AutomationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.automations.index');
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
    public function store(StoreAutomationRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Automation $automation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Automation $automation)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAutomationRequest $request, Automation $automation)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Automation $automation)
    {
        //
    }
}
