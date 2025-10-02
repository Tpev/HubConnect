<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use App\Models\DealRoom;
use App\Models\Team;

class CompanyConnection extends Model
{
    protected $table = 'company_connections';

    protected $fillable = [
        'company_a_id',
        'company_b_id',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    /* -------------------- Relationships -------------------- */

    public function companyA()
    {
        // Treat "company" as a Team record
        return $this->belongsTo(Team::class, 'company_a_id');
    }

    public function companyB()
    {
        // Treat "company" as a Team record
        return $this->belongsTo(Team::class, 'company_b_id');
    }

    /* -------------------- Helpers -------------------- */

    /** Always store/query the pair in normalized order (min, max). */
    public static function normalizePair(int $a, int $b): array
    {
        return [min($a, $b), max($a, $b)];
    }

    /** Scope to a specific pair regardless of direction. */
    public function scopeBetween(Builder $q, int $a, int $b): Builder
    {
        [$min, $max] = self::normalizePair($a, $b);

        return $q->where('company_a_id', $min)
                 ->where('company_b_id', $max);
    }

    /** True if the two companies (teams) are already connected. */
    public static function areConnected(int $a, int $b): bool
    {
        if ($a === $b) {
            return true; // same team = trivially connected to self
        }

        return static::query()->between($a, $b)->exists();
    }

    /**
     * Create (or fetch) a connection between two companies (teams).
     * Uses normalized (min,max) ordering to avoid duplicates.
     * Ensures a Deal Room exists for this pair.
     */
    public static function connectPair(int $a, int $b): self
    {
        if ($a === $b) {
            // Avoid self-connection; return a non-persisted instance.
            return static::query()->firstOrNew();
        }

        [$min, $max] = self::normalizePair($a, $b);

        $connection = static::query()->firstOrCreate([
            'company_a_id' => $min,
            'company_b_id' => $max,
        ]);

        // Ensure a Deal Room exists for the pair (store original ids; normalize happens inside)
        $room = DealRoom::forPair($a, $b, createdBy: $a);

        // Optionally annotate connection metadata with deal room info (created timestamp + id)
        $meta = $connection->metadata ?? [];
        if (empty($meta['deal_room_id'])) {
            $meta['deal_room_id'] = $room->id;
            $meta['deal_room_created_at'] = now()->toDateTimeString();
            $connection->metadata = $meta;
            $connection->save();
        }

        return $connection;
    }

    /**
     * Convenience: fetch (or lazily create) the Deal Room for this connection.
     * Note: creation typically handled in connectPair(); this is safe to call anywhere.
     */
    public function dealRoom(): DealRoom
    {
        return DealRoom::forPair($this->company_a_id, $this->company_b_id);
    }
}
