<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class AnnouncementMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectText;
    public $messageText;

    public function __construct($subject, $message)
    {
        $this->subjectText = $subject;
        $this->messageText = $message;
    }

    public function envelope()
    {
        return new Envelope(
            subject: $this->subjectText,
        );
    }

    public function content()
    {
        return new Content(
            view: 'emails.announcement',
        );
    }

    public function attachments()
    {
        return [];
    }
}
