<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycRejected extends Notification
{
    public function __construct(public Team $team, public string $reason) {}

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Verification update')
            ->greeting('A quick update on your verification')
            ->line('We’re unable to approve the company at this time.')
            ->line('Reason: ' . $this->reason)
            ->action('Edit profile', route('companies.profile.edit', $this->team))
            ->line('Reply to this email if you have questions.');
    }
}
