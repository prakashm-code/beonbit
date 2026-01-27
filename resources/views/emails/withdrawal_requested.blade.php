<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Withdrawal Request</title>
</head>
<body style="font-family: Arial, sans-serif; background-color:#f4f4f4; padding:20px;">
    <table width="100%" cellpadding="0" cellspacing="0">
        <tr>
            <td align="center">
                <table width="600" cellpadding="0" cellspacing="0"
                       style="background:#ffffff; padding:30px; border-radius:6px;">

                    <!-- Header -->
                    <tr>
                        <td style="text-align:center;">
                            <h2 style="color:#333;">New Withdrawal Request</h2>
                        </td>
                    </tr>

                    <!-- Body -->
                    <tr>
                        <td style="padding:20px 0; color:#555;">
                            <p>Hello <strong>Admin</strong>,</p>

                            <p>
                                A user has requested a wallet withdrawal.
                                Below are the details:
                            </p>
                        </td>
                    </tr>

                    <!-- Details -->
                    <tr>
                        <td style="padding:5px 0; color:#555;">
                            <p><strong>User Email:</strong> {{ $data['email'] }}</p>
                            <p><strong>Requested Amount:</strong> ${{ $data['amount'] }}</p>
                            <p><strong>Commission:</strong> ${{ $data['commission'] }}</p>
                            <p><strong>Net Amount:</strong> ${{ $data['net_amount'] }}</p>
                            <p><strong>Status:</strong> {{ ucfirst($data['status']) }}</p>
                            {{-- <p><strong>Withdrawal ID:</strong> {{ $data['withdrawal_id'] }}</p> --}}
                        </td>
                    </tr>

                    <!-- Action Button -->
                    <tr>
                        <td align="center" style="padding:20px 0;">
                            <a href="{{ url('/admin/withdraw_request') }}"
                               style="background:#28a745; color:#ffffff; text-decoration:none;
                                      padding:12px 25px; border-radius:4px; display:inline-block;">
                                Review Withdrawal
                            </a>
                        </td>
                    </tr>

                    <!-- Footer note -->
                    <tr>
                        <td style="color:#777; font-size:14px;">
                            <p>
                                Please login to the admin panel to approve or reject this request.
                            </p>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="padding-top:20px; color:#999; font-size:13px;">
                            <p>Regards,<br><strong>Infinitewealth </strong></p>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>
</body>
</html>
