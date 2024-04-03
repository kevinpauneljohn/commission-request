<?php
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function(){
    Route::resource('commission-voucher',\App\Http\Controllers\CommissionVoucherController::class);
    Route::get('/commission-voucher-preview',[\App\Http\Controllers\CommissionVoucherController::class,'preview'])->name('commission-voucher-preview');
    Route::get('/commission-voucher-lists',[\App\Http\Controllers\CommissionVoucherController::class,'voucherLists'])->name('commission-voucher-lists');
    Route::get('/commission-voucher-lists/{request_id}',[\App\Http\Controllers\CommissionVoucherController::class,'voucherListsByRequestId'])->name('commission-voucher-lists-by-request');
    Route::post('/commission-voucher/approve/{commission_voucher}',[\App\Http\Controllers\CommissionVoucherController::class,'approveVoucher'])->name('approve-commission-voucher');
    Route::put('/commission-voucher/payment/{commission_voucher}',[\App\Http\Controllers\CommissionVoucherController::class,'savePayment'])->name('commission-voucher-payment');
    Route::get('/commission-voucher/print/{commission_voucher}',[\App\Http\Controllers\CommissionVoucherController::class,'printVoucher'])->name('print-commission-voucher');
    Route::get('/commission-voucher/download/{commission_voucher}',[\App\Http\Controllers\CommissionVoucherController::class,'downLoadVoucher'])->name('download-commission-voucher');
});
