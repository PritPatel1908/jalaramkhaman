<?php

namespace App\Mail;

use App\Models\RecurringOrderSchedule;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class RecuringOrderNotifyEmail extends Mailable
{
    use Queueable, SerializesModels;
    public RecurringOrderSchedule $recurringOrderSchedule;
    public User $user;

    /**
     * Create a new message instance.
     */
    public function __construct(RecurringOrderSchedule $recurring_order_schedule, User $user)
    {
        $this->recurringOrderSchedule = $recurring_order_schedule;
        $this->user = $user;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Recuring Order Notify Email',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'view.mail.RecuringOrderNotifyEmail',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
