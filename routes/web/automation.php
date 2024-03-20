<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('automation',\App\Http\Controllers\AutomationController::class);
    Route::get('/automation-lists',[\App\Http\Controllers\AutomationController::class,'automationLists'])->name('automation-lists');
});
