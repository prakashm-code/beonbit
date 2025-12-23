<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f4f4; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0" style="background:#ffffff; padding:30px; border-radius:6px;">

                    <tr>
                        <td style="text-align:center;">
                            <h2 style="color:#333;">Reset Your Password</h2>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding:20px 0; color:#555;">
                            <p>Hello <strong>{{ $user->name ?? 'User' }}</strong>,</p>

                            <p>
                                You requested to reset your password.
                                Click the button below to proceed.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td align="center" style="padding:20px 0;">
                            <a href="{{ $resetLink }}"
                               style="background:#007bff; color:#ffffff; text-decoration:none;
                                      padding:12px 25px; border-radius:4px; display:inline-block;">
                                Reset Password
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td style="color:#777; font-size:14px;">
                            <p>
                                This link will expire in <strong>2 minutes</strong>.
                                If you did not request this, please ignore this email.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding-top:20px; color:#999; font-size:13px;">
                            <p>Regards,<br>BeonBit Team</p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
