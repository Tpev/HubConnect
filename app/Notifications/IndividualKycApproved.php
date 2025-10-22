<?php

namespace App\Notifications;

use App\Models\KycSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IndividualKycApproved extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public KycSubmission $submission) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your identity verification is approved')
            ->greeting('Good news!')
            ->line('Your individual verification has been approved.')
            ->action('Browse Jobs', url('/jobs'));
    }
}
