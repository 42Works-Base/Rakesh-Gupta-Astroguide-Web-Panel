<!DOCTYPE html>
<html>

<head>
    <title>Reset Your Password</title>
</head>

<body>
    <p>Hello,</p>
    <p>You have requested to reset your password. Click the link below to reset it:</p>

    <p>
        <a href="{{ url('/reset-password/' . $token) }}">Reset Password</a>
    </p>

    <p>If you did not request this, please ignore this email.</p>

    <p>Thank you,</p>
    <p>AstroGuide Team</p>
</body>

</html>