<?php

namespace App\Notifications;

use App\Models\KycSubmission;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class IndividualKycRejected extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public KycSubmission $submission, public string $reason) {}

    public function via($notifiable): array { return ['mail']; }

    public function toMail($notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Your identity verification needs attention')
            ->greeting('Hi,')
            ->line('Your verification was rejected.')
            ->line('Reason: '.$this->reason)
            ->action('Update & Resubmit', url('/kyc/individual'));
    }
}
