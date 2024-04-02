<?php

namespace App\Services;

use App\Models\CommissionVoucher;
use App\Models\Request;
use Rmunate\Utilities\SpellNumber;
use Yajra\DataTables\DataTables;

class CommissionVoucherService
{
    private function requestModel($request_id): Request
    {
        return Request::findOrFail($request_id);
    }

    public function voucher($request)
    {
        $voucher = $this->requestModel($request->request_id);
        $gross_commission_value = $this->is_reference_amount_box_checked($request)? $request->reference_amount : $voucher->total_contract_price;
        $gross_commission = $this->gross_commission($gross_commission_value, $voucher->sd_rate);
        $gross_commission_based_on_tcp = $this->gross_commission($voucher->total_contract_price, $voucher->sd_rate);
        $tax_basis = $this->is_reference_amount_box_checked($request);
        $wht = $request->wht;
        $vat = $request->vat;
        $percentage_released = $this->is_reference_amount_box_checked($request) ? $request->percentage_released_reference_amount :
            $request->percentage_released;
        $released_gross_commission = $this->released_gross_commission($gross_commission, $percentage_released);
        $net_commission_less_wht = $this->net_commission_less_wht($released_gross_commission, $wht);
        $net_commission_less_vat = $this->net_commission_less_vat($net_commission_less_wht, $vat);

        $with_holding_tax_amount = $this->with_holding_tax_amount($released_gross_commission, $wht);
        $vat_amount = $this->vat_amount($net_commission_less_wht, $vat);
        $commission_receivable = !$this->is_reference_amount_box_checked($request) ? $net_commission_less_vat :
            $this->net_commission_tax_reference_based($gross_commission_based_on_tcp, $with_holding_tax_amount, $vat_amount);

        return collect([
            'prepared_by' => ucwords(auth()->user()->full_name),
            'tax_basis_reference' => $tax_basis,
            'tax_basis_reference_remarks' => $request->remarks,
            'tax_basis_reference_amount' => '₱ '.number_format($request->reference_amount,2),
            'request_number' => $voucher->formatted_id,
            'request_id' => $voucher->id,
            'category' => $request->category,
            'project' => $voucher->project,
            'payee' => ucwords($voucher->user->full_name),
            'client' => ucwords($voucher->buyer_full_name),
            'tcp' => '₱ '.number_format($voucher->total_contract_price,2),
            'sd_rate' => number_format($voucher->sd_rate,2).'%',
            'gross_commission' => '₱ '.number_format($this->is_reference_amount_box_checked($request) ? $gross_commission_based_on_tcp : $gross_commission,2),
            'percentage_released' => number_format($percentage_released,2),
            'released_gross_commission' => '₱ '.number_format($released_gross_commission,2),
            'sub_total' => '₱ '.$this->sub_total($gross_commission, $percentage_released),
            'with_holding_tax' => '('.number_format($wht,2).'%)',
            'with_holding_tax_amount' => '₱ '.number_format($with_holding_tax_amount,2),
            'vat' => '('.number_format($vat,2).'%)',
            'vat_amount' => '₱ '.number_format($vat_amount,2),
            'net_commission_less_wht' => '₱ '.number_format($net_commission_less_wht,2),
            'net_commission_less_vat' => '₱ '.number_format($net_commission_less_vat,2),
            'total_commission' => '₱ '.number_format($commission_receivable,2),
            'deductions' => $this->deductions($request),
            'deduction_lists' => $this->deduction_lists($request),
            'commission_receivable' => '₱ '.number_format($commission_receivable - $this->deductions($request),2),
            'commission_in_words' => ucwords(SpellNumber::value(round($commission_receivable - $this->deductions($request),2))->locale('en')->currency('Pesos')->toMoney()),
        ])->toJson();
    }

    private function deduction_lists($request): array
    {
        $amount = collect($request->deduction_amount)->map(function (int $value, int $key) {
            return '- (₱ '.number_format($value,2).')';
        });
        return collect($request->deduction_title)->combine($amount)->all();
    }
    private function deductions($request)
    {
        return collect($request->deduction_amount)->sum();
    }
    private function net_commission_tax_reference_based($gross_commission_based_on_tcp, $with_holding_tax_amount, $vat_amount)
    {
        return $gross_commission_based_on_tcp - ($with_holding_tax_amount + $vat_amount);
    }

    private function is_reference_amount_box_checked($request): bool
    {
        return collect($request->all())->has('reference_amount_checkbox');
    }
    private function released_gross_commission($gross_commission, $percentage_released): float|int
    {
        return $gross_commission * ($percentage_released / 100);
    }

    private function commission_receivable($commission_receivable)
    {
        return $commission_receivable;
    }

    private function with_holding_tax_amount($gross_commission, $wht): float|int
    {
        return $gross_commission * ($wht / 100);
    }
    private function net_commission_less_wht($gross_commission, $wht): float|int
    {
        $converted_wht = 100 - $wht;
        return $gross_commission * ($converted_wht / 100);
    }

    private function gross_commission($tcp, $sd_rate): float|int
    {
        return $tcp * ($sd_rate / 100);
    }

    private function sub_total($gross_commission, $percentage_released): float|int
    {
        return $gross_commission * ($percentage_released / 100);
    }

    private function vat_amount($net_commission_less_wht, $vat): float|int
    {
        $vat_formula = (100 + 12) / 100;
        $vat_percent_format = $vat / 100;
        return ( $net_commission_less_wht / $vat_formula ) * $vat_percent_format;
    }
    public function net_commission_less_vat($net_commission_less_wht, $vat): float
    {
        $vat_formula = (100 + 12) / 100;
        $vat_percent_format = $vat / 100;
        return $net_commission_less_wht - (( $net_commission_less_wht / $vat_formula ) * $vat_percent_format);
    }

    public function save_voucher($request)
    {
        return CommissionVoucher::create([
            'category' => $request->category,
            'request_id' => $request->request_id,
            'voucher' => $request->all(),
            'user_id' => auth()->user()->id,
            'is_approved' => false
        ]);
    }

    public function commission_voucher_lists($query): \Illuminate\Http\JsonResponse
    {
        return DataTables::of($query)
            ->editColumn('voucher',function($voucher){
                $badge = $voucher->is_approved?'<span class="badge badge-success">Approved</span>':'<span class="badge badge-warning">Pending</span>';
                $details = '<form id="voucher-payment-form"><table class="table table-responsive table-bordered table-hover">';
                $details .= '<tr><td colspan="4" class="text-bold text-center">'.$badge.'</td></tr>';
                $details .= '<tr><td colspan="4" class="text-bold text-center">RNH Realty & Management Inc. Comm Voucher</td></tr>';
                $details .= '<tr><td colspan="4" class="text-center text-bold">'.$voucher->voucher->project.'</td></tr>';
                $details .= '<tr><td>Payee</td><td>'.$voucher->voucher->payee.'</td><td>Amount</td><td class="w-25">'.$voucher->voucher->commission_receivable.'</td></tr>';
                $details .= '<tr><td>Client</td><td>'.$voucher->voucher->client.'</td><td>In Words</td><td>'.$voucher->voucher->commission_in_words.'</td></tr>';
                $details .= '<tr><td colspan="4" class="table-active"></td></tr>';
                $details .= '<tr><td colspan="2">TCP</td><td colspan="2">'.$voucher->voucher->tcp.'</td></tr>';
                $details .= '<tr><td colspan="2">SD Rate</td><td colspan="2">'.$voucher->voucher->sd_rate.'</td></tr>';
                $details .= '<tr><td colspan="2">Gross Commission</td><td colspan="2">'.$voucher->voucher->gross_commission.'</td></tr>';

                    if($voucher->voucher->tax_basis_reference == 'true')
                    {
                        $details .= '<tr><td colspan="2">'.$voucher->voucher->tax_basis_reference_remarks.'</td><td colspan="2">'.$voucher->voucher->tax_basis_reference_amount.'</td></tr>';
                    }
                $details .= '<tr><td colspan="2">'.$voucher->voucher->percentage_released.'% Released</td><td colspan="2">'.$voucher->voucher->released_gross_commission.'</td></tr>';
                $details .= '<tr><td colspan="2">Withholding Tax '.$voucher->voucher->with_holding_tax.'</td><td colspan="2">'.$voucher->voucher->with_holding_tax_amount.'</td></tr>';
                $details .= '<tr><td colspan="2">VAT '.$voucher->voucher->vat.'</td><td colspan="2">'.$voucher->voucher->vat_amount.'</td></tr>';
                $details .= '<tr><td colspan="2">Net Commission</td><td colspan="2">'.$voucher->voucher->net_commission_less_vat.'</td></tr>';
                if($voucher->voucher->deductions > 0)
                {
                    foreach ($voucher->voucher->deduction_lists as $key => $deductions){
                        $details .= '<tr><td colspan="2">'.$key.'</td><td colspan="2" class="text-danger">'.$deductions.'</td></tr>';
                    }
                    $details .= '<tr><td colspan="2">Total Commission Balance</td><td colspan="2">'.$voucher->voucher->commission_receivable.'</td></tr>';
                }
                $details .= '<tr><td colspan="4" class="table-active"></td></tr>';
                $details .= '<tr><td colspan="2">Approved By</td><td colspan="2">'.(is_null($voucher->approvedBy)? '': ucwords($voucher->approvedBy->full_name)).'</td></tr>';
                $details .= '<tr><td colspan="2">Prepared By</td><td colspan="2">'.$voucher->voucher->prepared_by.'</td></tr>';
                if($voucher->is_approved)
                {
                    if(auth()->user()->hasRole('sales director'))
                    {
                        $details .= '<tr>
                                <td><label>Payment Type</label><br/><span class="text-bold text-primary">'.$voucher->payment_type.'</span></td>
                                <td><label>Issuer</label><br/><span class="text-bold text-primary">'.$voucher->issuer.'</span></td>
                                <td><label>Reference/Cheque #</label><br/><span class="text-bold text-primary">'.$voucher->transaction_reference_no.'</span></td>
                                <td><label>Amount Transferred</label><br/><span class="text-bold text-primary">₱ '.number_format($voucher->amount_transferred,2).'</span></td>
                                </tr>';
                    }else{
                        $max_amount = floatval(preg_replace("/[^-0-9\.]/","",$voucher->voucher->commission_receivable));
                        $payment_type  = '<select class="form-control" name="payment_type" id="payment_type">';
                        $payment_type .= '<option value="">--select payment type--</option>';
                        $payment_type .= '<option value="Bank Transfer"'.($voucher->payment_type == 'Bank Transfer'?'selected':'').'>Bank Transfer</option>';
                        $payment_type .= '<option value="GCash" '.($voucher->payment_type == 'GCash'?'selected':'').'>GCash</option>';
                        $payment_type .= '<option value="PayMaya" '.($voucher->payment_type == 'PayMaya'?'selected':'').'>PayMaya</option>';
                        $payment_type .= '<option value="Cheque Payment" '.($voucher->payment_type == 'Cheque Payment'?'selected':'').'>Cheque Payment</option>';
                        $payment_type .= '</select>';
                        $details .= '<tr id="payment-fields">
                            <td><div class="form-group payment_type"><label>Payment Type</label>'.$payment_type.'</div></td>
                            <td><div class="form-group issuer"><label>Issued thru</label><input type="text" class="form-control" name="issuer" id="issuer" value="'.$voucher->issuer.'" required></div></td>
                            <td><div class="form-group transaction_reference_no"><label>Reference/Cheque #</label><input type="text" class="form-control" name="transaction_reference_no" id="transaction_reference_no" value="'.$voucher->transaction_reference_no.'" required></div></td>
                            <td><div class="form-group amount_transferred"><label>Amount Transferred</label><input type="number" class="form-control" step="any" min="0" max="'.$max_amount.'" name="amount_transferred" id="amount_transferred" value="'.(is_null($voucher->amount_transferred) ? $max_amount : $voucher->amount_transferred).'" required></div></td></tr>';
                    }

                    if(!auth()->user()->hasRole('sales director'))
                    {
                        $details .= '<tr><td colspan="4"><div class="form-group drive_link"><label>Drive link</label><input type="url" name="drive_link" id="drive_link" class="form-control" value="'.$voucher->drive_link.'"></td></tr>';
                        $disabled = 'disabled';
                        if(is_null($voucher->payment_type) || is_null($voucher->issuer) || is_null($voucher->transaction_reference_no) || is_null($voucher->amount_transferred))
                        {
                            $disabled = '';
                        }
                        $details .= '<tr><td colspan="4"><input type="hidden" name="voucher_id" value="'.$voucher->id.'"><button type="submit" class="btn btn-success voucher-payment-btn w-100" '.$disabled.'>Save</button></td></tr>';
                        if(auth()->user()->can('edit commission voucher') && !is_null($voucher->payment_type) || !is_null($voucher->issuer) || !is_null($voucher->transaction_reference_no) || !is_null($voucher->amount_transferred))
                        {
                            $details .= '<tr><td colspan="4"><button type="button" class="btn btn-default btn-sm mr-1 edit-voucher-payment-btn">Edit</button><button type="button" class="btn btn-default btn-sm cancel-edit-voucher mr-1">Cancel</button><a href="'.(is_null($voucher->drive_link) ? '#' : $voucher->drive_link ).'" target="_blank" class="btn btn-info btn-sm mr-1">Access Drive</a></td></tr>';
                        }
                    }
                }

                $details .= '</table></form>';
                return $details;
            })
            ->addColumn('action',function($voucher){
                $action = "";

                if(auth()->user()->can('view commission voucher') && $voucher->request->status == "delivered" && auth()->user()->hasRole('sales director'))
                {
                    $action .= '<a href="'.$voucher->drive_link.'" target="_blank" class="btn btn-sm btn-success m-1" id="'.$voucher->id.'" title="Access Drive"><i class="fa fa-folder-open"></i></a>';
                }
                if(auth()->user()->can('approve commission voucher') && !$voucher->is_approved)
                {
                    $action .= '<button class="btn btn-sm btn-success approve-voucher-btn m-1" id="'.$voucher->id.'" title="Approve"><i class="fa fa-check"></i></button>';
                }
                if(auth()->user()->can('delete commission voucher') && !$voucher->is_approved)
                {
                    $action .= '<button class="btn btn-sm btn-danger delete-voucher-btn m-1" id="'.$voucher->id.'" title="Delete"><i class="fa fa-trash"></i></button>';
                }
                if(auth()->user()->can('view commission voucher') && $voucher->request->status == 'delivered' || $voucher->request->status == 'completed')
                {
                    $action .= '<a href="'.$voucher->drive_link.'" target="_blank" class="btn btn-sm btn-success drive-btn m-1" id="'.$voucher->id.'" title="'.(is_null($voucher->drive_link)? 'No folder url saved' : 'Access Folder').'"><i class="fa fa-folder-open"></i></a>';
                }
                if(auth()->user()->can('print commission voucher'))
                {
                    $action .= '<a href="'.route('print-commission-voucher',['commission_voucher' => $voucher->id]).'" target="_blank" class="btn btn-sm btn-primary voucher-btn m-1" id="'.$voucher->id.'" title="Print"><i class="fa fa-print"></i></a>';
                }

                return $action;
            })
            ->rawColumns(['action','voucher'])
            ->make(true);
    }

    public function approve($voucher): bool
    {
        $voucher->is_approved = true;
        $voucher->approver = auth()->user()->id;
        return (bool)$voucher->save();
    }

    public function save_payment($voucher, $request): bool
    {
        $voucher->payment_type = $request->payment_type;
        $voucher->issuer = $request->issuer;
        $voucher->transaction_reference_no = $request->transaction_reference_no;
        $voucher->amount_transferred = $request->amount_transferred;
        $voucher->drive_link = $request->drive_link;
        return (bool)$voucher->save();
    }

    public function request_status_delivered($requestModel): bool
    {
        $requestModel->status = 'delivered';
        return (bool)$requestModel->save();
    }

    public function request_status_completed($requestModel): bool
    {
        $requestModel->status = 'completed';
        return (bool)$requestModel->save();
    }

}
