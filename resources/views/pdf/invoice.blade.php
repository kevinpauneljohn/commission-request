<html lang="">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <meta name="csrf-token" content="{{csrf_token()}}">
    <title>
        Print Voucher
    </title>
    <link rel="stylesheet" href="{{public_path('/vendor/adminlte/dist/css/adminlte.min.css')}}">

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
<table style="width: 100%!important; font-size: 8pt">
    <tbody>
    <tr><td colspan="4">{{$voucher->request_number}}</td></tr>
    <tr><td colspan="4" class="text-bold text-center">RNH Realty &amp; Management Inc. Comm Voucher</td></tr>
    <tr><td colspan="4" class="text-center text-bold">{{ucwords(strtolower($voucher->project))}}</td></tr>
    <tr>
        <td>Payee</td><td>{{$voucher->payee}}</td>
        <td>Amount</td><td>{{str_replace('₱', '', $voucher->commission_receivable)}} Php</td>
    </tr>
    <tr>
        <td>Client</td><td>{{$voucher->client}}</td>
        <td>In Words</td><td style="width: 30%">{{$voucher->commission_in_words}}</td>
    </tr>
    <tr><td colspan="4" class="table-active"></td></tr>
    <tr><td colspan="2">TCP</td><td colspan="2">{{str_replace('₱', '', $voucher->tcp)}} Php</td></tr>
    <tr><td colspan="2">SD Rate</td><td colspan="2">{{$voucher->sd_rate}}</td></tr>
    <tr><td colspan="2">Gross Commission</td><td colspan="2">{{str_replace('₱', '', $voucher->gross_commission)}} Php</td></tr>
    @if($voucher->tax_basis_reference == 'true')
        <tr><td colspan="2">{{$voucher->tax_basis_reference_remarks}}</td>
            <td colspan="2">{{str_replace('₱', '', $voucher->tax_basis_reference_amount)}} Php</td></tr>
    @endif

    <tr>
        <td colspan="2">{{$voucher->percentage_released}}% Released</td>
        <td colspan="2">{{str_replace('₱', '', $voucher->released_gross_commission)}} Php</td>
    </tr>
    <tr>
        <td colspan="2">Withholding Tax {{$voucher->with_holding_tax}}</td>
        <td colspan="2">{{str_replace('₱', '', $voucher->with_holding_tax_amount)}} Php</td>
    </tr>
    <tr>
        <td colspan="2">VAT {{$voucher->vat}}</td>
        <td colspan="2">{{str_replace('₱', '', $voucher->vat_amount)}} Php</td>
    </tr>
    <tr>
        <td colspan="2">Net Commission</td>
        <td colspan="2">{{str_replace('₱', '', $voucher->net_commission_less_vat)}} Php</td>
    </tr>
    @if($voucher->deductions > 0)
        @foreach ($voucher->deduction_lists as $key => $deductions)
            <tr>
                <td colspan="2">{{$key}}</td>
                <td colspan="2" class="text-danger">{{str_replace('₱', '', $deductions)}} Php</td>
            </tr>
        @endforeach
        <tr>
            <td colspan="2">Total Commission Balance</td>
            <td colspan="2">{{str_replace('₱', '', $voucher->commission_receivable)}} Php</td>
        </tr>
    @endif
    <tr><td colspan="4"></td></tr>
    <tr><td colspan="2">Prepared By</td><td colspan="2">{{$voucher->prepared_by}}</td></tr>
    <tr><td colspan="2">Approved By</td><td colspan="2"></td></tr>
    <tr>
        <td><label>Payment Type</label><br><span class="text-bold text-primary">{{$payment_type}}</span></td>
        <td><label>Issued thru</label><br><span class="text-bold text-primary">{{$issuer}}</span></td>
        <td><label>Reference/Cheque #</label><br><span class="text-bold text-primary">{{$transaction_reference_no}}</span></td>
        <td><label>Amount Transferred</label><br><span class="text-bold text-primary">{{number_format($amount_transferred,2)}} Php</span></td>
    </tr>
    </tbody>
</table>

</body>
</html>
