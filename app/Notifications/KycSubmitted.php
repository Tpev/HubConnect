<?php

namespace App\Notifications;

use App\Models\Team;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class KycSubmitted extends Notification
{
    public function __construct(public Team $team) {}

    public function via(object $notifiable): array { return ['mail']; }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('We’re reviewing your company')
            ->greeting('Thanks for submitting your verification')
            ->line('We verify each company to keep the network trusted.')
            ->line('Most reviews finish within one business day.')
            ->action('View status', route('kyc.gate'))
            ->line('You’ll get an email as soon as we approve your account.');
    }
}
