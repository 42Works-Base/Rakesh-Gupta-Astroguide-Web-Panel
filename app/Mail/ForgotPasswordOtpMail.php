<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ForgotPasswordOtpMail extends Mailable
{
    use Queueable, SerializesModels;

    public $otp;
    public $user;

    public function __construct($user, $otp)
    {
        $this->otp = $otp;
        $this->user = $user;
    }

    public function build()
    {
        return $this->subject('Reset Your Password - ' . config('app.name'))
            ->view('emails.forgot_password_otp')
            ->with([
                'otp' => $this->otp,
                'user' => $this->user,
                'appName' => config('app.name'),
            ]);
    }
}
