<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Verify Your Email</title>
</head>
<body style="margin:0;padding:0;background-color:#f4f6f8;">

<table width="100%" cellpadding="0" cellspacing="0" style="background:#f4f6f8;padding:30px 0;">
    <tr>
        <td align="center">

            <table width="100%" cellpadding="0" cellspacing="0"
                   style="max-width:480px;background:#ffffff;border-radius:8px;
                          box-shadow:0 4px 10px rgba(0,0,0,0.1);padding:30px;">

                <!-- Header -->
                <tr>
                    <td align="center" style="padding-bottom:20px;">
                        <h2 style="margin:0;color:#333;font-family:Arial,sans-serif;">
                            Email Verification
                        </h2>
                    </td>
                </tr>

                <!-- Body -->
                <tr>
                    <td style="font-family:Arial,sans-serif;color:#555;font-size:15px;line-height:1.6;">
                        <p>Hello <strong>{{ $name }}</strong>,</p>

                        <p>
                            Thank you for registering with us.
                            Please confirm your email address by clicking the button below.
                        </p>
                    </td>
                </tr>

                <!-- Button -->
                <tr>
                    <td align="center" style="padding:25px 0;">
                        <a href="{{ $verifyUrl }}"
                           style="
                                background:#28a745;
                                color:#ffffff;
                                text-decoration:none;
                                padding:12px 28px;
                                border-radius:5px;
                                font-weight:bold;
                                font-family:Arial,sans-serif;
                                display:inline-block;
                           ">
                            Verify Email
                        </a>
                    </td>
                </tr>

                <!-- Footer -->
                <tr>
                    <td style="font-family:Arial,sans-serif;color:#888;font-size:13px;">
                        <p>
                            If you did not create an account, no further action is required.
                        </p>

                        <p style="margin-top:20px;">
                            Regards,<br>
                            <strong>{{ config('app.name') }}</strong>
                        </p>
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>

</body>
</html>
