<html lang="">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width">
    <title>New Task Created</title>
    <style>
        body{
            padding: 20px;
        }
    </style>
</head>
<body>
<div style="background-color: ghostwhite; margin: 100px; padding: 20px;">

    <h4>{{ucfirst($task->title)}}</h4>
    <p>
        Click <a href="{{route('task.show',['task' => $task->id])}}">here</a> to view the task
    </p>
</div>
</body>
</html>
