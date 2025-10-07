<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Laravel\Jetstream\Events\TeamCreated;
use Laravel\Jetstream\Events\TeamDeleted;
use Laravel\Jetstream\Events\TeamUpdated;
use Laravel\Jetstream\Team as JetstreamTeam;

class Team extends JetstreamTeam
{
    /** @use HasFactory<\Database\Factories\TeamFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'personal_team','hq_country','website','official_email_domain','company_type','registration_number','linkedin_url',
    'kyc_status','kyc_submitted_at','kyc_verified_at','kyc_reviewer_user_id','kyc_notes','is_listed',
    ];

    /**
     * The event map for the model.
     *
     * @var array<string, class-string>
     */
    protected $dispatchesEvents = [
        'created' => TeamCreated::class,
        'updated' => TeamUpdated::class,
        'deleted' => TeamDeleted::class,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'personal_team' => 'boolean',
			    'kyc_submitted_at' => 'datetime',
    'kyc_verified_at'  => 'datetime',
	'is_listed' => 'boolean',
        ];
    }
	public function hasActiveLicense(): bool
{
    return (bool) $this->license_active;
}
// Tiny helpers
public function isKycApproved(): bool { return $this->kyc_status === 'approved'; }
public function isKycPending(): bool { return in_array($this->kyc_status, ['new','pending_review']); }
public function kycStatusLabel(): string {
    return match ($this->kyc_status) {
        'approved' => 'Approved',
        'pending_review','new' => 'Pending review',
        'rejected' => 'Rejected',
        'suspended' => 'Suspended',
        default => ucfirst($this->kyc_status ?? 'Unknown'),
    };
}



}
