<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('automation-task',\App\Http\Controllers\AutomationTaskController::class);
    Route::get('/automation-task-lists/{automation_id}',[\App\Http\Controllers\AutomationTaskController::class,'automationTaskList'])->name('automation-task-list');
    Route::put('/change-sequence/{automation_id}/{task_template_id}/{sequence_number}',[\App\Http\Controllers\AutomationTaskController::class,'updateSequence'])->name('change-task-sequence');
});
