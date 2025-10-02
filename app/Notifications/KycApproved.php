<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycApproved extends Notification
{
    public function __construct(public Team $team) {}

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your company is verified')
            ->greeting('Welcome to the Club 🎉')
            ->line('Your company has been approved.')
            ->action('Start exploring', route('companies.index'))
            ->line('You can now connect and open Deal Rooms.');
    }
}
