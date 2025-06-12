<!DOCTYPE html>
<html>

<head>
    <title>Password Reset OTP</title>
</head>

<body>
    <p>Hello {{ ucfirst($user->first_name)}},</p>
    <p>You requested to reset your password for <strong>{{ $appName }}</strong>.</p>
    <p>Your OTP code is: <strong>{{ $otp }}</strong></p>
    <!-- <p>This OTP is valid for only 5 minutes.</p> -->
    <p>If you didn't request this, please ignore this email.</p>
    <br>
    <p>Thanks,</p>
    <p>{{ $appName }} Team</p>
</body>

</html>