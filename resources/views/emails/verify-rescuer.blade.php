<h1>Hello {{ $rescuer->name }},</h1>

<p>Thank you for registering. Please verify your email by clicking the link below:</p>

<p>
    <a href="{{ $verificationUrl }}">Verify Email</a>
</p>

<p>If you didn't create an account, no further action is required.</p>