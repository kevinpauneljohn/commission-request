<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('finding',\App\Http\Controllers\FindingController::class);
    Route::get('/finding-list/{requestId}',[\App\Http\Controllers\FindingController::class,'findingsList'])->name('finding-list');
    Route::post('/create-findings',[\App\Http\Controllers\FindingController::class,'create_findings'])->name('create-findings');
});
