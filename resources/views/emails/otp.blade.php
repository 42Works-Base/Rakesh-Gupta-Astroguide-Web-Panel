<!DOCTYPE html>
<html>

<head>
    <title>Verify Your Account - {{ $appName }}</title>
</head>

<body>
    <p>Hello {{ ucfirst($user->first_name)}},</p>
    <p>Your One-Time Password (OTP) for verification is: <strong>{{ $otp }}</strong></p>
    <p>Please enter this OTP to verify your account.</p>
    <p>If you did not request this, please ignore this email.</p>
    <p>Thanks,<br>
        <strong>{{ $appName }}</strong>
    </p>
</body>

</html>