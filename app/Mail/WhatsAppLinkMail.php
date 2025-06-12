<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class WhatsAppLinkMail extends Mailable
{
    use Queueable, SerializesModels;

    public $whatsappLink;

    public function __construct($whatsappLink)
    {
        $this->whatsappLink = $whatsappLink;
    }

    public function build()
    {
        return $this->subject('Join Your Scheduled Call')
            ->view('emails.whatsapp_link')
            ->with(['whatsappLink' => $this->whatsappLink]);
    }
}
