<?php

namespace App\Services;

use App\Models\CommissionVoucher;
use App\Models\Request;
use Rmunate\Utilities\SpellNumber;

class CommissionVoucherService
{
    private function requestModel($request_id): Request
    {
        return Request::findOrFail($request_id);
    }

    public function voucher($request)
    {
//        return collect($request->deduction_amount)->sum();
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
            'net_commission_less_vat' => '₱ '.$net_commission_less_vat,
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
        ]);
    }

}
