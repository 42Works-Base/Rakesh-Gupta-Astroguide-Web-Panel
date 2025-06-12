<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SupportMail extends Mailable
{
    use Queueable, SerializesModels;

    public $support;

    /**
     * Create a new message instance.
     */
    public function __construct($support)
    {
        $this->support = $support;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('New Support Request')
            ->view('emails.support_mail')
            ->with(['support' => $this->support]);
    }
}
