<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>
        Print Voucher
    </title>
    <link rel="stylesheet" href="{{asset('/vendor/adminlte/dist/css/adminlte.min.css')}}">

    <style type="text/css">
        table, td{
            border: solid 1px #5c5e5e;
            border-collapse: collapse;
        }
        th, td {
            padding: 3px;
        }

        @media print {
            @page {
                size: A4;
                margin: 10px!important;
            }

            body {
                padding: 15px; /* This will act as your margin. Originally, the margin will hide the header and footer text. */
            }
        }


    </style>
</head>
<body>
<table style="width: 100%!important; font-size: 12pt">
    <tbody>
    <tr><td colspan="4">{{$commissionVoucher->voucher->request_number}}</td></tr>
    <tr><td colspan="4" class="text-bold text-center">RNH Realty &amp; Management Inc. Comm Voucher</td></tr>
    <tr><td colspan="4" class="text-center text-bold">{{$commissionVoucher->voucher->project}}</td></tr>
    <tr>
        <td>Payee</td><td>{{$commissionVoucher->voucher->payee}}</td>
        <td>Amount</td><td>{{$commissionVoucher->voucher->commission_receivable}}</td>
    </tr>
    <tr>
        <td>Client</td><td>{{$commissionVoucher->voucher->client}}</td>
        <td>In Words</td><td style="width: 30%">{{$commissionVoucher->voucher->commission_in_words}}</td>
    </tr>
    <tr><td colspan="4" class="table-active"></td></tr>
    <tr><td colspan="2">TCP</td><td colspan="2">{{$commissionVoucher->voucher->tcp}}</td></tr>
    <tr><td colspan="2">SD Rate</td><td colspan="2">{{$commissionVoucher->voucher->sd_rate}}</td></tr>
    <tr><td colspan="2">Gross Commission</td><td colspan="2">{{$commissionVoucher->voucher->gross_commission}}</td></tr>
    @if($commissionVoucher->voucher->tax_basis_reference == 'true')
        <tr><td colspan="2">{{$commissionVoucher->voucher->tax_basis_reference_remarks}}</td>
            <td colspan="2">{{$commissionVoucher->voucher->tax_basis_reference_amount}}</td></tr>
    @endif

    <tr>
        <td colspan="2">{{$commissionVoucher->voucher->percentage_released}}% Released</td>
        <td colspan="2">{{$commissionVoucher->voucher->released_gross_commission}}</td>
    </tr>
    <tr>
        <td colspan="2">Withholding Tax {{$commissionVoucher->voucher->with_holding_tax}}</td>
        <td colspan="2">{{$commissionVoucher->voucher->with_holding_tax_amount}}</td>
    </tr>
    <tr>
        <td colspan="2">VAT {{$commissionVoucher->voucher->vat}}</td>
        <td colspan="2">{{$commissionVoucher->voucher->vat_amount}}</td>
    </tr>
    <tr>
        <td colspan="2">Net Commission</td>
        <td colspan="2">{{$commissionVoucher->voucher->net_commission_less_vat}}</td>
    </tr>
    @if($commissionVoucher->voucher->deductions > 0)
        @foreach ($commissionVoucher->voucher->deduction_lists as $key => $deductions)
            <tr>
                <td colspan="2">{{$key}}</td>
                <td colspan="2" class="text-danger">{{$deductions}}</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Total Commission Balance</td>
            <td colspan="2">{{$commissionVoucher->voucher->commission_receivable}}</td>
        </tr>
    @endif
    <tr><td colspan="4"></td></tr>
    <tr><td colspan="2">Prepared By</td><td colspan="2">{{$commissionVoucher->voucher->prepared_by}}</td></tr>
    <tr><td colspan="2">Approved By</td><td colspan="2">{{ucwords($commissionVoucher->approvedBy->full_name)}}</td></tr>
    <tr>
        <td><label>Payment Type</label><br><span class="text-bold text-primary">{{$commissionVoucher->payment_type}}</span></td>
        <td><label>Issued thru</label><br><span class="text-bold text-primary">{{$commissionVoucher->issuer}}</span></td>
        <td><label>Reference/Cheque #</label><br><span class="text-bold text-primary">{{$commissionVoucher->transaction_reference_no}}</span></td>
        <td><label>Amount Transferred</label><br><span class="text-bold text-primary">₱ {{number_format($commissionVoucher->amount_transferred,2)}}</span></td>
    </tr>
    </tbody>
</table>
<script src="{{asset('/vendor/jquery/jquery.min.js')}}"></script>
@can('print commission voucher')
    <script>
        $(document).ready(function(){
            window.print()
            setTimeout(window.close, 500);
        });
    </script>
@endcan
</body>
</html>
