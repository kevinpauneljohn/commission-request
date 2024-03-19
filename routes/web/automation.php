<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('automation',\App\Http\Controllers\AutomationController::class);
});
