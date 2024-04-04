<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>Request #{{$request->formatted_id}} has been delivered</title>
</head>
<body>

    <p style="font-size: 11pt; padding: 10px; background-color: white!important;">
        Request <span style="color: #0c84ff">#{{$request->formatted_id}}</span> has been delivered. Click
        <a href="{{route('request.show',['request' => $request->id])}}">here</a> and don't forget to mark it
        <span style="font-weight: bold; color: green">Completed</span>.
    </p>
</body>
</html>
