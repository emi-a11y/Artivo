<?php

namespace App\Mail;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/** Receipt email sent to the buyer after the admin confirms the payment. */
class OrderReceiptMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Your Artivo receipt, ' . $this->order->code);
    }

    public function content(): Content
    {
        return new Content(view: 'emails.receipt', text: 'emails.receipt-text');
    }
}
