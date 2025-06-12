<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OtpMail extends Mailable
{
    use Queueable, SerializesModels;
    public $user, $otp;
    /**
     * Create a new message instance.
     */
    public function __construct($user, $otp)
    {
        $this->otp = $otp;
        $this->user = $user;
    }

    public function build()
    {
        // dd($this->otp, $this->user);
        return $this->subject('Verify Your Account - ' . config('app.name'))
            ->view('emails.otp')
            ->with([
                'otp' => $this->otp,
                'user' => $this->user,
                'appName' => config('app.name'),
            ]);
    }
}
