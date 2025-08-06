<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
</head>
<body style="font-family: Arial, sans-serif; background-color: #f4f4f4; padding: 20px; margin: 0;">

    <table width="100%" cellpadding="0" cellspacing="0" border="0" style="max-width: 600px; margin: auto; background-color: #ffffff; border-radius: 8px; overflow: hidden;">
        <tr>
            <td style="background-color: #3B5770; padding: 20px; text-align: center;">
                <img src="https://stormresq.com/public/assets/stormresq-logo-c92b6e8c.png" alt="Rescue Logo" style="height: 60px;">
            </td>
        </tr>
        <tr>
            <td style="padding: 30px;">
                <h3 style="color: #333333;">Hello {{ $name }},</h3>

                <p style="font-size: 16px; color: #555555;">
                    We received a request to reset your password. You can reset it by clicking the button below:
                </p>

                <p style="text-align: center; margin: 30px 0;">
                    <a href="{{ $resetUrl }}" style="background-color: #E67E22; color: #ffffff; padding: 12px 24px; text-decoration: none; font-size: 16px; border-radius: 5px;">
                        Reset Password
                    </a>
                </p>

                <p style="font-size: 14px; color: #777777;">
                    If you didn’t request a password reset, please ignore this email. Your password will remain unchanged.
                </p>

                <p style="font-size: 14px; color: #777777;">
                    Regards,<br>
                    The StormResQ Team
                </p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f0f0f0; text-align: center; padding: 15px; font-size: 12px; color: #999999;">
                &copy; {{ date('Y') }} StormResQ Team. All rights reserved.
            </td>
        </tr>
    </table>

</body>
</html>