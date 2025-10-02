<?php

namespace App\Livewire\DealRooms;

use App\Models\DealRoom;
use App\Models\DealRoomMessage;
use App\Models\DealRoomParticipant;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;

class Show extends Component
{
    public DealRoom $room;

    #[Validate('required|string|min:1|max:2000')]
    public string $messageText = '';

    public bool $otherTyping = false;
    public bool $otherOnline = false;

    // 👇 Must be public + nullable so Livewire can hydrate them
    public ?int $companyId = null;
    public ?int $otherCompanyId = null;

    public function mount(DealRoom $room)
    {
        $companyId = $this->currentCompanyId();
        if (!$companyId || !$room->includesCompany($companyId)) {
            abort(403);
        }

        $this->companyId = $companyId;
        $this->otherCompanyId = $room->otherCompanyId($companyId);

        // Ensure participants exist
        $room->participants()->firstOrCreate(['company_id' => $companyId]);
        if ($this->otherCompanyId) {
            $room->participants()->firstOrCreate(['company_id' => $this->otherCompanyId]);
        }

        $this->room = $room->load([
            'companySmall',
            'companyLarge',
            'messages.company',
            'messages.user',
            'participants',
        ]);

        // Initial presence + read
        $this->touchPresence();
        $this->markAllRead();
        $this->computeStates();
    }

    public function send()
    {
        $this->validate();

        // Safety: ensure companyId is set (hydration guard)
        if (!$this->companyId) {
            $this->companyId = $this->currentCompanyId();
            if (!$this->companyId) abort(403);
        }

        DealRoomMessage::create([
            'deal_room_id' => $this->room->id,
            'company_id'   => $this->companyId,
            'user_id'      => Auth::id(),
            'body'         => trim($this->messageText),
        ]);

        $this->messageText = '';

        $this->room->load('messages.company', 'messages.user', 'participants');

        $this->touchPresence();
        $this->computeStates();
    }

    /** Called by wire:poll every 2s */
    public function tick()
    {
        // Safety: ensure IDs exist after hydration
        if (!$this->companyId) {
            $this->companyId = $this->currentCompanyId();
            if (!$this->companyId) abort(403);
            $this->otherCompanyId = $this->room->otherCompanyId($this->companyId);
        }

        $this->room->load('messages.company', 'messages.user', 'participants');
        $this->touchPresence();
        $this->markAllRead();
        $this->computeStates();
    }

    /** Input typing (debounced in the view) */
    public function setTyping()
    {
        if (!$this->companyId) return;
        $p = $this->room->participants()->firstOrCreate(['company_id' => $this->companyId]);
        $p->last_typing_at = now();
        $p->save();
    }

    public function render()
    {
        return view('livewire.deal-rooms.show')->layout('layouts.app');
    }

    protected function computeStates(): void
    {
        if (!$this->companyId) return;
        $this->otherTyping = $this->room->otherIsTyping($this->companyId);
        $this->otherOnline = $this->room->otherIsOnline($this->companyId);
    }

    protected function touchPresence(): void
    {
        if (!$this->companyId) return;
        /** @var DealRoomParticipant $p */
        $p = $this->room->participants()->firstOrCreate(['company_id' => $this->companyId]);
        $p->last_seen_at = now();
        $p->save();
    }

    /** Mark messages from the other side as read, update participant */
    protected function markAllRead(): void
    {
        if (!$this->companyId) return;

        /** @var DealRoomParticipant $p */
        $p = $this->room->participants()->firstOrCreate(['company_id' => $this->companyId]);
        $p->last_read_at = now();
        $p->save();

        // Mark unread messages from the other company as read
        DealRoomMessage::where('deal_room_id', $this->room->id)
            ->where('company_id', '!=', $this->companyId)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);
    }

    protected function currentCompanyId(): ?int
    {
        $user = Auth::user();
        if (!$user) return null;

        if (method_exists($user, 'currentTeam') && $user->currentTeam) {
            return (int) $user->currentTeam->id;
        }
        if (method_exists($user, 'ownedTeams')) {
            $owned = $user->ownedTeams()->first();
            if ($owned) return (int) $owned->id;
        }
        if (method_exists($user, 'teams')) {
            $any = $user->teams()->first();
            if ($any) return (int) $any->id;
        }
        if (isset($user->team_id) && $user->team_id) {
            return (int) $user->team_id;
        }
        if (isset($user->company_id) && $user->company_id) {
            return (int) $user->company_id;
        }

        return null;
    }
}
