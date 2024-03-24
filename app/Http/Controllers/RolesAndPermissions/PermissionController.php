<?php

namespace App\Http\Controllers\RolesAndPermissions;

use App\Http\Controllers\Controller;
use App\Http\Requests\PermissionRequest;
use App\Services\PermissionService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Yajra\DataTables\Facades\DataTables;

class PermissionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','permission:view permission'])->only(['index','show','permission_list']);
        $this->middleware(['auth','permission:add permission'])->only(['create','store']);
        $this->middleware(['auth','permission:edit permission'])->only(['edit','update']);
        $this->middleware(['auth','permission:delete permission'])->only(['delete']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::where('name','!=','super admin')->get();
        $permissions = Permission::all();
        return view('dashboard.permissions.index',compact('permissions','roles'));
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
    public function store(PermissionRequest $request, PermissionService $permissionService)
    {
        $permissionService->savePermission($request->all());
        return response()->json(['success' => true, 'message' => 'Permission successfully created']);
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
        $permission = Permission::findById($id);
        return collect($permission)->merge(['roles' => $permission->getRoleNames()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PermissionRequest $request, string $id, PermissionService $permissionService)
    {
        return $permissionService->updatePermission($request->all()) ?
            response()->json(['success' => true, 'message' => 'Permission Successfully Updated']) :
            response()->json(['success' => false, 'message' => 'An error occurred']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $permission = Permission::findById($id);
        $permission->syncRoles([]);
        return $permission->delete() ?
            response()->json(['success' => true, 'message' => 'Permission Successfully deleted']) :
            response()->json(['success' => false, 'message' => 'An error occurred']);
    }

    public function permission_list()
    {
        return DataTables::of(Permission::all())
            ->editColumn('updated_at', function($role){
                return $role->updated_at->format('M d, Y');
            })
            ->addColumn('roles', function($permission){
                $role = '';
                foreach ($permission->getRoleNames() as $roleName){
                    $role .= '<span class="badge badge-success mr-1">'.$roleName.'</span>';
                }
                return $role;
            })
            ->addColumn('action', function($permission){
                $action = '';

                if(auth()->user()->can('edit permission'))
                {
                    $action .= '<button class="btn btn-primary btn-xs mr-1 edit-permission" id="'.$permission->id.'">Edit</button>';
                }
                if(auth()->user()->can('delete permission'))
                {
                    $action .= '<button class="btn btn-danger btn-xs mr-1 delete-permission" id="'.$permission->id.'">Delete</button>';
                }
                return $action;
            })
            ->rawColumns(['action','roles'])
            ->make(true);
    }
}
