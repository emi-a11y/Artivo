<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Asks the buyer to confirm that this mailbox is really theirs, so receipts can be emailed to it. */
class VerifyEmailMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user, public string $url)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Confirm your email for Artivo');
    }

    public function content(): Content
    {
        return new Content(view: 'emails.verify', text: 'emails.verify-text');
    }
}
