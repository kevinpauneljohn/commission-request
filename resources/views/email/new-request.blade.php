<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Request Created</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="background-color:#f4f6f9; padding:40px 0;">
    <tr>
        <td align="center">

            <!-- MAIN CONTAINER -->
            <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.05);">

                <!-- HEADER -->
                <tr>
                    <td style="background:#0d6efd; padding:20px 30px; color:#ffffff;">
                        <h2 style="margin:0;">New Request Created</h2>
                        <p style="margin:5px 0 0; font-size:14px;">
                            Request ID:
                            <strong>{{ $request->formatted_id }}</strong>
                        </p>
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:30px;">

                        <p style="font-size:15px; color:#555; margin-top:0;">
                            A new request has been successfully submitted. Below are the complete details:
                        </p>

                        <!-- DETAILS TABLE -->
                        <table width="100%" cellpadding="10" cellspacing="0" style="border-collapse:collapse; font-size:14px;">

                            @php
                                $rows = [
                                    'Requester' => ucwords(strtolower($request->user->full_name)),
                                    'Buyer' => ucwords(strtolower($request->buyer->firstname)) . ' ' . ucwords(strtolower($request->buyer->lastname)),
                                    'Request Type' => $request->request_type,
                                    'Project' => ucwords(strtolower($request->project)),
                                    'Model Unit' => ucwords(strtolower($request->model_unit)),
                                    'Total Contract Price' => '₱ ' . number_format($request->total_contract_price,2),
                                    'Phase' => $request->phase,
                                    'Block / Lot' => 'Blk ' . $request->block . ' Lot ' . $request->lot,
                                    'Financing' => $request->financing,
                                    'SD Rate' => number_format($request->sd_rate,2) . '%',
                                    'Parent Request' => $request->parent_request ?? 'N/A',
                                ];
                            @endphp

                            @foreach($rows as $label => $value)
                                <tr>
                                    <td width="40%" style="background:#f8f9fa; font-weight:bold; border:1px solid #e9ecef;">
                                        {{ $label }}
                                    </td>
                                    <td style="border:1px solid #e9ecef;">
                                        {{ $value }}
                                    </td>
                                </tr>
                            @endforeach

                        </table>

                        <!-- MESSAGE SECTION -->
                        <div style="margin-top:25px;">
                            <h4 style="margin-bottom:10px;">Message</h4>
                            <div style="background:#f8f9fa; padding:15px; border-radius:5px; font-size:14px; color:#555;">
                                {{ $request->message }}
                            </div>
                        </div>

                        <!-- CTA BUTTON -->
                        <div style="text-align:center; margin-top:30px;">
                            <a href="{{ route('request.show',['request' => $request->id]) }}"
                               style="display:inline-block;
                                      background:#0d6efd;
                                      color:#ffffff;
                                      padding:12px 25px;
                                      text-decoration:none;
                                      border-radius:5px;
                                      font-weight:bold;
                                      font-size:14px;">
                                View Request
                            </a>
                        </div>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#f1f3f5; padding:20px 30px; text-align:center; font-size:12px; color:#888;">
                        This is an automated notification from your system.<br>
                        © {{ date('Y') }} Dream Home Guide Realty
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
