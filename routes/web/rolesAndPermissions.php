<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('role',\App\Http\Controllers\RolesAndPermissions\RoleController::class);
    Route::get('/role-lists',[\App\Http\Controllers\RolesAndPermissions\RoleController::class,'role_list'])->name('role-lists');

    Route::resource('permission',\App\Http\Controllers\RolesAndPermissions\PermissionController::class);
    Route::get('/permission-list',[\App\Http\Controllers\RolesAndPermissions\PermissionController::class,'permission_list'])->name('permission-list');
});
