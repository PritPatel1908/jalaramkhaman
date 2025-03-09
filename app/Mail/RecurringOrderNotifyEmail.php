<?php

namespace App\Mail;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\RecurringOrderSchedule;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class RecurringOrderNotifyEmail extends Mailable
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
            view: 'mails.notify-mail',
            with: ['order' => $this->recurringOrderSchedule, 'user' => $this->user]
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
