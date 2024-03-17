<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('finding',\App\Http\Controllers\FindingController::class);
});
