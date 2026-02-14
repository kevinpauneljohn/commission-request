<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>New Task Created</title>
</head>

<body style="margin:0; padding:0; background-color:#f4f6f9; font-family: Arial, Helvetica, sans-serif;">

<table width="100%" cellpadding="0" cellspacing="0" style="padding:40px 0; background-color:#f4f6f9;">
    <tr>
        <td align="center">

            <!-- EMAIL CONTAINER -->
            <table width="600" cellpadding="0" cellspacing="0"
                   style="background:#ffffff; border-radius:8px; overflow:hidden; box-shadow:0 4px 12px rgba(0,0,0,0.05);">

                <!-- HEADER -->
                <tr>
                    <td style="background:#0d6efd; padding:20px 30px; color:#ffffff;">
                        <h2 style="margin:0; font-weight:600;">New Task Created</h2>
                    </td>
                </tr>

                <!-- BODY -->
                <tr>
                    <td style="padding:30px;">

                        <!-- TASK TITLE -->
                        <h3 style="margin-top:0; color:#333333; font-size:18px;">
                            {{ ucfirst($task->title) }}
                        </h3>

                        <p style="color:#555555; font-size:14px; line-height:1.6;">
                            A new task has been created in the system.
                            Please review the details and take the necessary action.
                        </p>

                        <!-- CTA BUTTON -->
                        <div style="text-align:center; margin-top:30px;">
                            <a href="{{ route('task.show',['task' => $task->id]) }}"
                               style="display:inline-block;
                                      background:#0d6efd;
                                      color:#ffffff;
                                      padding:12px 28px;
                                      text-decoration:none;
                                      border-radius:6px;
                                      font-size:14px;
                                      font-weight:600;">
                                View Task
                            </a>
                        </div>

                    </td>
                </tr>

                <!-- FOOTER -->
                <tr>
                    <td style="background:#f1f3f5; padding:18px 30px; text-align:center; font-size:12px; color:#888888;">
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
