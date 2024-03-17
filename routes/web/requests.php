<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('/request',\App\Http\Controllers\Request\RequestController::class);
    Route::get('/request-list',[\App\Http\Controllers\Request\RequestController::class,'request_list'])->name('request-list');
    Route::get('/request-activities/{requestId}',[\App\Http\Controllers\Request\RequestController::class,'requestActivities'])->name('request-activities');
});
