<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('/request',\App\Http\Controllers\Request\RequestController::class);
    Route::get('/request-list',[\App\Http\Controllers\Request\RequestController::class,'request_list'])->name('request-list');
    Route::get('/request-activities/{requestId}',[\App\Http\Controllers\Request\RequestController::class,'requestActivities'])->name('request-activities');
    Route::get('/request/get-parent/{requestId}',[\App\Http\Controllers\Request\RequestController::class,'getParentRequest'])->name('get-parent-request');
    Route::put('/request-declined/{requestId}',[\App\Http\Controllers\Request\RequestController::class,'declineRequest'])->name('request-declined');
});
