<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DealRoom extends Model
{
    protected $fillable = [
        'company_small_id',
        'company_large_id',
        'created_by_company_id',
        'meta',
        'closed_at',
    ];

    protected $casts = [
        'meta' => 'array',
        'closed_at' => 'datetime',
    ];

    public function companySmall()
    {
        return $this->belongsTo(Team::class, 'company_small_id');
    }

    public function companyLarge()
    {
        return $this->belongsTo(Team::class, 'company_large_id');
    }

    public function messages()
    {
        return $this->hasMany(DealRoomMessage::class)->orderBy('created_at', 'asc');
    }

    public function participants()
    {
        return $this->hasMany(DealRoomParticipant::class, 'deal_room_id');
    }

    public static function normalizePair(int $a, int $b): array
    {
        return [min($a, $b), max($a, $b)];
    }

    public static function forPair(int $a, int $b, ?int $createdBy = null): self
    {
        [$small, $large] = static::normalizePair($a, $b);

        /** @var self $room */
        $room = static::query()->firstOrCreate(
            ['company_small_id' => $small, 'company_large_id' => $large],
            ['created_by_company_id' => $createdBy]
        );

        // Ensure both participants exist
        $room->participants()->firstOrCreate(['company_id' => $small]);
        $room->participants()->firstOrCreate(['company_id' => $large]);

        return $room;
    }

    public function scopeForCompany(Builder $q, int $companyId): Builder
    {
        return $q->where('company_small_id', $companyId)
                 ->orWhere('company_large_id', $companyId);
    }

    public function includesCompany(int $companyId): bool
    {
        return $this->company_small_id === $companyId || $this->company_large_id === $companyId;
    }

    public function otherCompanyId(int $myCompanyId): ?int
    {
        if (!$this->includesCompany($myCompanyId)) return null;
        return $this->company_small_id === $myCompanyId ? $this->company_large_id : $this->company_small_id;
    }

    public function otherCompany(int $myCompanyId): ?Team
    {
        $otherId = $this->otherCompanyId($myCompanyId);
        if (!$otherId) return null;
        return $this->company_small_id === $otherId ? $this->companySmall : $this->companyLarge;
    }

    public function participantFor(int $companyId): ?DealRoomParticipant
    {
        return $this->participants->firstWhere('company_id', $companyId)
            ?? $this->participants()->where('company_id', $companyId)->first();
    }

    /** Unread messages for company: messages from the other party after last_read_at */
    public function unreadCountFor(int $companyId): int
    {
        $p = $this->participantFor($companyId);
        $since = $p?->last_read_at ?? Carbon::createFromTimestamp(0);

        return $this->messages()
            ->where('company_id', '!=', $companyId)
            ->where('created_at', '>', $since)
            ->count();
    }

    /** Presence (other side considered "online" if seen in last 2 mins) */
    public function otherIsOnline(int $myCompanyId): bool
    {
        $other = $this->participantFor($this->otherCompanyId($myCompanyId) ?? 0);
        return $other && $other->last_seen_at && $other->last_seen_at->gt(now()->subMinutes(2));
    }

    /** Typing (other typing if last_typing_at within 6s) */
    public function otherIsTyping(int $myCompanyId): bool
    {
        $other = $this->participantFor($this->otherCompanyId($myCompanyId) ?? 0);
        return $other && $other->last_typing_at && $other->last_typing_at->gt(now()->subSeconds(6));
    }
	    public function files(): HasMany
    {
        return $this->hasMany(DealRoomFile::class, 'room_id');
    }
}
