<?php

namespace App\Http\Controllers;

use App\Models\CommissionVoucher;
use App\Http\Requests\StoreCommissionVoucherRequest;
use App\Http\Requests\UpdateCommissionVoucherRequest;
use App\Services\CommissionVoucherService;
use Illuminate\Http\Request;

class CommissionVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','permission:view commission voucher'])->only(['index','show','preview']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreCommissionVoucherRequest $request, CommissionVoucherService $commissionVoucherService)
    {
        if(!is_null($request->category) && $request->commission_receivable !== "₱ 0.00")
        {
            if($commissionVoucherService->save_voucher($request))
                return response()->json(['success' => true, 'message' => 'Voucher successfully saved']);
        }
        return response()->json(['success' => false, 'message' => 'Cannot save voucher']);
    }

    /**
     * Display the specified resource.
     */
    public function show(CommissionVoucher $commissionVoucher)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(CommissionVoucher $commissionVoucher)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCommissionVoucherRequest $request, CommissionVoucher $commissionVoucher)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CommissionVoucher $commissionVoucher)
    {
        //
    }

    public function preview(Request $request, CommissionVoucherService $commissionVoucherService)
    {
        return $commissionVoucherService->voucher($request);
    }
}
