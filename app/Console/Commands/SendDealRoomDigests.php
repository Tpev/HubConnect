<?php

namespace App\Console\Commands;

use App\Models\DealRoom;
use App\Models\DealRoomParticipant;
use App\Models\DealRoomMessage;
use App\Models\Team;
use App\Notifications\DealRoomDigestNotification;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;

class SendDealRoomDigests extends Command
{
    protected $signature = 'dealrooms:send-digests';
    protected $description = 'Send smart email digests for deal rooms (no per-message spam).';

    public function handle(): int
    {
        // Strategy:
        // For each participant p:
        //  - skip if notify_mode = mute
        //  - compute unread count for p.company_id
        //  - skip if 0 unread
        //  - skip if p.last_seen_at within last 10 minutes (they’re active)
        //  - throttle: skip if last_email_at within cooldown
        //  - build previews (last 5 unread from other company)
        //  - recipients = team owner + team users (dedup)
        //  - send one email digest
        //  - set last_email_at = now

        $now = now();
        $activeWindow = $now->copy()->subMinutes(10);

        DealRoomParticipant::query()
            ->with(['room.messages.company', 'company.owner', 'company.users'])
            ->chunkById(500, function ($participants) use ($now, $activeWindow) {
                foreach ($participants as $p) {
                    /** @var DealRoomParticipant $p */
                    if ($p->notify_mode === 'mute') {
                        continue;
                    }

                    $room = $p->room;
                    if (!$room) continue;

                    // unread count for this company
                    $unreadCount = $room->unreadCountFor($p->company_id);
                    if ($unreadCount <= 0) {
                        continue;
                    }

                    // user is active recently? skip
                    if ($p->last_seen_at && $p->last_seen_at->gt($activeWindow)) {
                        continue;
                    }

                    // throttle
                    $cooldown = $p->email_cooldown_minutes ?: 60;
                    if ($p->last_email_at && $p->last_email_at->gt($now->copy()->subMinutes($cooldown))) {
                        continue;
                    }

                    // build previews: last 5 unread from other company
                    $previews = DealRoomMessage::query()
                        ->where('deal_room_id', $room->id)
                        ->where('company_id', '!=', $p->company_id)
                        ->where(function ($q) use ($p) {
                            $q->whereNull('read_at')->orWhere('created_at', '>', $p->last_read_at ?? Carbon::createFromTimestamp(0));
                        })
                        ->latest()
                        ->limit(5)
                        ->get()
                        ->map(function ($m) {
                            return [
                                'who' => $m->company?->name ?? 'Partner',
                                'body' => str($m->body)->limit(120)->toString(),
                                'at' => $m->created_at->format('Y-m-d H:i'),
                            ];
                        })
                        ->reverse() // chronological
                        ->values()
                        ->all();

                    // recipients: team owner + team users
                    $team = $p->company;
                    if (!$team) continue;

                    $recipients = collect([$team->owner])->filter();
                    if (method_exists($team, 'users')) {
                        $recipients = $recipients->merge($team->users);
                    }
                    $recipients = $recipients->unique('id');

                    if ($recipients->isEmpty()) {
                        continue;
                    }

                    $otherName = ($room->company_small_id === $p->company_id)
                        ? ($room->companyLarge?->name ?? 'Partner')
                        : ($room->companySmall?->name ?? 'Partner');

                    Notification::send(
                        $recipients,
                        new DealRoomDigestNotification($room, $unreadCount, $previews, $otherName)
                    );

                    $p->last_email_at = now();
                    $p->save();
                }
            });

        $this->info('Deal room digests processed.');
        return Command::SUCCESS;
    }
}
