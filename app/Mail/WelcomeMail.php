<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Str;

/** The welcome email sent once, when a new account is created (sign-up form or first Google sign-in). */
class WelcomeMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public User $user)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Welcome to Artivo, ' . Str::before($this->user->name, ' ') . '!',
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.welcome',
            text: 'emails.welcome-text',
        );
    }
}
