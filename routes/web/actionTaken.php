<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('action-taken',\App\Http\Controllers\ActionTakenController::class);
//    Route::get('/task-list',[\App\Http\Controllers\TaskController::class,'tasks'])->name('task-list');
//    Route::get('/get-task-by-request/{requestId}',[\App\Http\Controllers\TaskController::class,'getTaskByRequestId'])->name('get-task-by-request');
//    Route::put('/update-task-status',[\App\Http\Controllers\TaskController::class,'updateStatus'])->name('update-task-status');
//    Route::get('/task-action-taken/{task}',[\App\Http\Controllers\TaskController::class,'taskActionTakens'])->name('task-action-taken');
});
