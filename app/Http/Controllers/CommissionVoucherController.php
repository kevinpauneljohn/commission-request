<?php

namespace App\Http\Controllers;

use App\Http\Requests\VoucherPaymentRequest;
use App\Models\CommissionVoucher;
use App\Http\Requests\StoreCommissionVoucherRequest;
use App\Http\Requests\UpdateCommissionVoucherRequest;
use App\Services\CommissionVoucherService;
use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class CommissionVoucherController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','permission:view commission voucher'])->only(['index','show','preview','voucherLists','voucherListsByRequestId']);
        $this->middleware(['auth','permission:approve commission voucher'])->only(['approveVoucher']);
        $this->middleware(['auth','permission:edit commission voucher'])->only(['savePayment']);
        $this->middleware(['auth','permission:print commission voucher'])->only(['printVoucher','downLoadVoucher']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('dashboard.vouchers.index');
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
    public function destroy(CommissionVoucher $commissionVoucher): \Illuminate\Http\JsonResponse
    {
        return $commissionVoucher->delete() ?
            response()->json(['success' => true, 'message' => 'Voucher successfully deleted']) :
            response()->json(['success' => false, 'message' => 'An error occurred']) ;
    }

    public function preview(Request $request, CommissionVoucherService $commissionVoucherService): string
    {
        return $commissionVoucherService->voucher($request);
    }

    public function voucherLists(CommissionVoucherService $commissionVoucherService): \Illuminate\Http\JsonResponse
    {
        $vouchers = CommissionVoucher::where('is_approved',false)->get();
        return $commissionVoucherService->commission_voucher_lists($vouchers);
    }

    public function voucherListsByRequestId($request_id, CommissionVoucherService $commissionVoucherService): \Illuminate\Http\JsonResponse
    {
        $vouchers = CommissionVoucher::where('request_id',$request_id)->get();
        return $commissionVoucherService->commission_voucher_lists($vouchers);
    }

    public function approveVoucher(CommissionVoucher $commissionVoucher, CommissionVoucherService $commissionVoucherService): \Illuminate\Http\JsonResponse
    {
        return $commissionVoucherService->approve($commissionVoucher) ?
            response()->json(['success' => true, 'message' => 'Voucher has been approved!']) :
            response()->json(['success' => false, 'message' => 'An error occurred!']);
    }

    public function savePayment(VoucherPaymentRequest $request, CommissionVoucher $commissionVoucher, CommissionVoucherService $commissionVoucherService): \Illuminate\Http\JsonResponse
    {
        return $commissionVoucherService->save_payment($commissionVoucher, $request) ?
            response()->json(['success' => true, 'message' => 'Payment successfully recorded!']) :
            response()->json(['success' => false, 'message' => 'An error occurred!']);
    }

    public function printVoucher(CommissionVoucher $commissionVoucher): \Illuminate\Contracts\View\View|\Illuminate\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\Foundation\Application
    {
        return view('dashboard.vouchers.print-voucher',compact('commissionVoucher'));
    }

    public function downLoadVoucher(CommissionVoucher $commissionVoucher): \Illuminate\Http\Response
    {
        $pdf = Pdf::loadView('pdf.invoice', collect($commissionVoucher)->toArray());
        return $pdf->download('commission_voucher_'.$commissionVoucher->request->formatted_id.'.pdf');
    }
}
