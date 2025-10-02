<?php

namespace App\Notifications;

use App\Models\DealRoom;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DealRoomDigestNotification extends Notification
{
    public function __construct(
        public DealRoom $room,
        public int $unreadCount,
        public array $previews,       // array of ['who' => string, 'body' => string, 'at' => string]
        public string $otherCompanyName
    ) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $url = route('deal-rooms.show', $this->room);

        $mail = (new MailMessage)
            ->subject("New activity in Deal Room — {$this->otherCompanyName}")
            ->greeting("You have {$this->unreadCount} unread message(s) in {$this->otherCompanyName} ↔ " . ($this->room->companySmall?->name ?? 'Your team'))
            ->line('Recent messages:');

        foreach ($this->previews as $p) {
            $mail->line("• {$p['who']} ({$p['at']}): {$p['body']}");
        }

        $mail->action('Open Deal Room', $url)
             ->line('You receive this digest based on your notification settings (smart mode).');

        return $mail;
    }
}
