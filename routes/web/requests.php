<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('/request',\App\Http\Controllers\Request\RequestController::class);
    Route::get('/request-list',[\App\Http\Controllers\Request\RequestController::class,'request_list'])->name('request-list');
    Route::get('/request-activities/{requestId}',[\App\Http\Controllers\Request\RequestController::class,'requestActivities'])->name('request-activities');
    Route::get('/request/get-parent/{requestId}',[\App\Http\Controllers\Request\RequestController::class,'getParentRequest'])->name('get-parent-request');
    Route::put('/request-declined/{requestId}',[\App\Http\Controllers\Request\RequestController::class,'declineRequest'])->name('request-declined');
    Route::put('/request-delivered/{request}',[\App\Http\Controllers\Request\RequestController::class,'approveRequest'])->name('request-delivered');
    Route::put('/request-completed/{request}',[\App\Http\Controllers\Request\RequestController::class,'completeRequest'])->name('request-completed');
    Route::put('/request-status-update/{request}',[\App\Http\Controllers\Request\RequestController::class,'updateStatus'])->name('request-status-update');
    Route::post('/display-request',[\App\Http\Controllers\Request\RequestController::class,'setRequestToDisplay'])->name('display-request');
});
