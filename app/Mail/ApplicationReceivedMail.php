<?php

namespace App\Mail;

use App\Models\Application;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ApplicationReceivedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public Application $application
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: __('mail.application_received_subject', ['name' => config('clan.name')]),
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.application-received',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
