<?php

namespace App\Mail;

use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use MailerSend\Helpers\Builder\Personalization;
use MailerSend\Helpers\Builder\Variable;
use MailerSend\LaravelDriver\MailerSendTrait;

class CheckoutCompleted extends Mailable
{
    use Queueable, SerializesModels, MailerSendTrait;

    /**
     * Create a new message instance.
     */
    public function __construct(public Collection $orders)
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Thank you for your purshase',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        $to = Arr::get($this->to, '0.address');

        

        return new Content(
            view: 'emails.checkout_completed',

        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
        ];
    }
}
