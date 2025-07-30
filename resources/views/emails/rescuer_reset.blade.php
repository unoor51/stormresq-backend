<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8">
  <title>Reset Password</title>
</head>
<body style="font-family: Arial, sans-serif;">
  <div>
    <img src="{{ asset('images/stormresq-logo.png') }}" alt="StormResQ Logo" style="height: 60px; margin-bottom: 20px;">

    <h4>Hello {{ $name }},</h4>
    <p style="margin-bottom: 20px;">You requested to reset your password.</p>
    
    <a href="{{ $resetUrl }}" style="background: #ff6600; color: white; padding: 10px 20px; text-decoration: none; border-radius: 4px;">Reset Password</a>

    <p style="margin-top: 20px;">Regards,<br>StormResQ Team</p>
  </div>
</body>
</html>