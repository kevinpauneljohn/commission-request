<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\Request;
use App\DataTables\UsersDataTable;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware(['permission:view user'])->only(['index']);
    }
    /**
     * Display a listing of the resource.
     */
    public function index(UsersDataTable $dataTable)
    {
//        return view('dashboard.user.index');
        $backendRoles = Role::where('name','!=','super admin')->get();
       return $dataTable->render('dashboard.user.index',compact('backendRoles'));
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
    public function store(UserRequest $request, UserService $userService)
    {
        if($userService->addUser($request->all()) === true)
        {
            return response()->json([
                'success' => true,
                'message' => 'New user successfully added!'
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'An error occurred!'
        ]);
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
        $user = User::findOrFail($id);
        return collect($user)->merge(['roles' => $user->getRoleNames()]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id, UserService $userService)
    {
        return $userService->updateUser($request->all(), $id) ?
            response()->json(['success' => true, 'message' => 'User successfully updated']) :
            response()->json(['success' => false, 'message' => 'No changes made']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        return User::findOrFail($id)->delete() ? response()->json(['success' => true]) : response()->json(['success' => false]);
    }
}
