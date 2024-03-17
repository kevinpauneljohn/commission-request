<?php

namespace App\Http\Controllers\RolesAndPermissions;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class RoleController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','permission:view role'])->only(['index','role_list']);
        $this->middleware(['auth','permission:add role'])->only(['store']);
        $this->middleware(['auth','permission:edit role'])->only(['edit','update']);
        $this->middleware(['auth','permission:delete role'])->only(['destroy']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.roles.index');
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
    public function store(Request $request)
    {
        $request->validate([
            'role' => 'required|unique:roles,name',
        ]);

        return Role::create(['name' => $request->role]) ?
            response()->json(['success' => true, 'message' => 'Role successfully created!']) :
            response()->json(['success' => false, 'message' => 'An error occurred']);
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Role::findById($id);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'role' => 'required',
        ]);

        $role = Role::findById($id);
        $role->name = $request->role;
        if($role->isDirty())
        {
            $role->save();
            return response()->json(['success' => true, 'message' => 'Role successfully updated!']);
        }
        return response()->json(['success' => false, 'message' => 'No changes made']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return Role::findById($id)->delete() ?
            response()->json(['success' => true, 'message' => 'Role successfully deleted']) :
            response()->json(['success' => false, 'message' => 'An error occurred!']);
    }

    public function role_list()
    {
        return DataTables::of(Role::where('name','!=','super admin'))
            ->editColumn('updated_at', function($role){
                return $role->updated_at->format('M d, Y');
            })
            ->editColumn('name', function($role){
                return ucwords($role->name);
            })
            ->addColumn('action', function($role){
                $action = '';

                if(auth()->user()->can('edit role'))
                {
                    $action .= '<button class="btn btn-primary btn-xs mr-1 edit-role" id="'.$role->id.'">Edit</button>';
                }
                if(auth()->user()->can('delete role'))
                {
                    $action .= '<button class="btn btn-danger btn-xs mr-1 delete-role" id="'.$role->id.'">Delete</button>';
                }
                return $action;
            })
            ->rawColumns(['action'])
            ->make(true);
    }
}
