<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class MatchRequest extends Model
{
    /** ─────────────────────────────────────────────────────────
     |  Constants
     |  ────────────────────────────────────────────────────────*/
    public const STATUS_PENDING  = 'pending';
    public const STATUS_ACCEPTED = 'accepted';
    public const STATUS_DECLINED = 'declined';

    /** ─────────────────────────────────────────────────────────
     |  Mass assignment / casts / defaults
     |  ────────────────────────────────────────────────────────*/
    protected $fillable = [
        'from_company_id',
        'to_company_id',
        'status',
        'context',
        'note',
        'metadata',
        // pair_min / pair_max are computed automatically; keep them out of $fillable
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    protected $attributes = [
        'status' => self::STATUS_PENDING,
    ];

    /** ─────────────────────────────────────────────────────────
     |  Relationships
     |  ────────────────────────────────────────────────────────*/
    public function fromCompany()
    {
        return $this->belongsTo(Company::class, 'from_company_id');
    }

    public function toCompany()
    {
        return $this->belongsTo(Company::class, 'to_company_id');
    }

    /** ─────────────────────────────────────────────────────────
     |  Global lifecycle: keep pair_min / pair_max correct
     |  ────────────────────────────────────────────────────────*/
    protected static function booted(): void
    {
        $compute = function (MatchRequest $m): void {
            if ($m->from_company_id && $m->to_company_id) {
                $a = (int) $m->from_company_id;
                $b = (int) $m->to_company_id;
                $m->pair_min = min($a, $b);
                $m->pair_max = max($a, $b);
            }
        };

        static::creating($compute);
        static::updating($compute);
        static::saving($compute); // extra safety if status changes post-create
    }

    /** ─────────────────────────────────────────────────────────
     |  Query scopes
     |  ────────────────────────────────────────────────────────*/

    /** Limit to rows involving a given company (either side). */
    public function scopeForCompany(Builder $q, int $companyId): Builder
    {
        return $q->where(function ($qq) use ($companyId) {
            $qq->where('from_company_id', $companyId)
               ->orWhere('to_company_id', $companyId);
        });
    }

    /** Limit to pair regardless of direction by using pair_min/pair_max. */
    public function scopeBetween(Builder $q, int $a, int $b): Builder
    {
        [$min, $max] = [min($a, $b), max($a, $b)];
        return $q->where('pair_min', $min)->where('pair_max', $max);
    }

    /** Status helpers. */
    public function scopePending(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_PENDING);
    }

    public function scopeAccepted(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_ACCEPTED);
    }

    public function scopeDeclined(Builder $q): Builder
    {
        return $q->where('status', self::STATUS_DECLINED);
    }

    /** Pending between two companies, regardless of direction. */
    public function scopePendingBetween(Builder $q, int $a, int $b): Builder
    {
        return $q->between($a, $b)->pending();
    }

    /** Any status between two companies, newest first. */
    public function scopeHistoryBetween(Builder $q, int $a, int $b): Builder
    {
        return $q->between($a, $b)->orderByDesc('id');
    }

    /** ─────────────────────────────────────────────────────────
     |  Convenience methods
     |  ────────────────────────────────────────────────────────*/

    /** True if a PENDING request exists for this pair (either direction). */
    public static function hasPendingBetween(int $a, int $b): bool
    {
        return static::query()->pendingBetween($a, $b)->exists();
    }

    /** True if ANY request exists for this pair with a specific status. */
    public static function hasStatusBetween(int $a, int $b, string $status): bool
    {
        return static::query()->between($a, $b)->where('status', $status)->exists();
    }

    /** Create a pending request safely (lets DB unique prevent dup). */
    public static function createPending(int $fromId, int $toId, ?string $note = null, ?array $metadata = null): self
    {
        return static::create([
            'from_company_id' => $fromId,
            'to_company_id'   => $toId,
            'status'          => self::STATUS_PENDING,
            'note'            => $note,
            'metadata'        => $metadata,
        ]);
    }
}
