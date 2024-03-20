<?php

namespace App\Http\Controllers;

use App\Models\Automation;
use App\Http\Requests\StoreAutomationRequest;
use App\Http\Requests\UpdateAutomationRequest;
use App\Models\User;
use App\Services\AutomationService;
use Spatie\Permission\Models\Role;

class AutomationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view automation'])->only(['index','show','automationLists']);
        $this->middleware(['permission:add automation'])->only(['store']);
        $this->middleware(['permission:edit automation'])->only(['edit','update']);
        $this->middleware(['permission:delete automation'])->only(['destroy']);
    }
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
    public function store(StoreAutomationRequest $request, AutomationService $automationService)
    {
        return $automationService->create($request, auth()->user()->id) ?
            response()->json(['success' => true, 'message' => 'Automation template successfully created!']) :
            response()->json(['success' => false, 'message' => 'Automation template successfully created!']) ;
    }

    /**
     * Display the specified resource.
     */
    public function show(Automation $automation)
    {
        $roles = Role::where('name','!=','super admin')->where('name','!=','sales director')->get();
        return view('dashboard.automations.show',compact('roles','automation'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Automation $automation)
    {
        return $automation;
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateAutomationRequest $request, Automation $automation)
    {
        $automation->title = $request->title;
        if($automation->isDirty())
        {
            $automation->save();
            return response()->json(['success' => true, 'message' => 'Automation template successfully updated!']);
        }
        return response()->json(['success' => false, 'message' => 'No changes made!']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Automation $automation)
    {
        if($automation->automationTasks->count() == 0)
        {
            $automation->delete();
            return response()->json(['success' => true, 'message' => 'Automation template successfully deleted!']);
        }
        return response()->json(['success' => false, 'message' => 'Template cannot be deleted if there is an existing save task']);
    }

    public function automationLists(AutomationService $automationService)
    {
        return $automationService->lists(Automation::all());
    }
}
