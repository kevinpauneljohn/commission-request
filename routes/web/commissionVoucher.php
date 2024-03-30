<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('commission-voucher',\App\Http\Controllers\CommissionVoucherController::class);
    Route::get('/commission-voucher-preview',[\App\Http\Controllers\CommissionVoucherController::class,'preview'])->name('commission-voucher-preview');
});
