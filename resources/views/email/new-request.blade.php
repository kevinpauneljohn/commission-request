<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>New Request Created</title>
    <style>
        body{
            padding: 20px;
        }
        table, th, td {
            border: 1px solid darkgrey;
            border-collapse: collapse;

        }
        th, td {
            padding: 15px;
        }
    </style>
</head>
<body>
<div style="background-color: ghostwhite; margin: 100px;">
    <p>
        A request <span style="color:#0c84ff; font-weight: bold">{{$request->formatted_id}}</span> has been created!. Click the link <a href="{{route('request.show',['request' => $request->id])}}">here</a> to access the Request
    </p>
    <table style="width: 100%; background-color: white;">
        <tr><td>Requester</td><td>{{ucwords(strtolower($request->user->full_name))}}</td></tr>
        <tr><td>Buyer</td><td>{{ucwords(strtolower($request->buyer->firstname))}} {{ucwords(strtolower($request->buyer->lastname))}}</td></tr>
        <tr><td>Request Type</td><td>{{$request->request_type}}</td></tr>
        <tr><td>Project</td><td>{{ucwords(strtolower($request->project))}}</td></tr>
        <tr><td>Model Unit</td><td>{{ucwords(strtolower($request->model_unit))}}</td></tr>
        <tr><td>Total Contract Price</td><td>₱ {{number_format($request->total_contract_price,2)}}</td></tr>
        <tr><td>Phase</td><td>{{$request->phase}}</td></tr>
        <tr><td>Block/Lot</td><td>Blk {{$request->block}} Lot {{$request->lot}}</td></tr>
        <tr><td>Financing</td><td>{{$request->financing}}</td></tr>
        <tr><td>SD Rate</td><td>{{number_format($request->sd_rate,2)}}%</td></tr>
        <tr><td>Parent Request</td><td>{{$request->parent_request}}</td></tr>
        <tr><td colspan="2"><h4>Message</h4><p>{{$request->message}}</p></td></tr>
    </table>
</div>
</body>
</html>
