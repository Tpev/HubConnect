<?php

namespace App\Models;

use Laravel\Jetstream\Team;

class Company extends Team
{
    protected $table = 'teams';

    protected $fillable = [
        'name','slug','company_type','website','hq_country','year_founded',
        'headcount','stage','summary','team_profile_photo_path',
    ];

    protected $casts = [
        'company_type' => 'string',
        'year_founded' => 'integer',
        'headcount'    => 'integer',
    ];

    // Relationships
    public function specialties() {
        return $this->belongsToMany(Specialty::class, 'company_specialties', 'company_id', 'specialty_id')
            ->withPivot('depth')
            ->withTimestamps();
    }

    public function certifications() {
        return $this->belongsToMany(Certification::class, 'company_certifications', 'company_id', 'certification_id')
            ->withPivot(['verified_at','verified_by'])
            ->withTimestamps();
    }

    public function contacts() {
        return $this->hasMany(CompanyContact::class, 'company_id');
    }

    public function assets() {
        return $this->hasMany(CompanyAsset::class, 'company_id');
    }

    public function intents() {
        return $this->hasMany(CompanyIntent::class, 'company_id');
    }

    public function activeIntent() {
        return $this->intents()->where('status','active')->latest('id')->first();
    }

    public function isConnectedWith(self $other): bool
    {
        return \App\Models\CompanyConnection::areConnected($this->id, $other->id);
    }

    public function members()
    {
        // Jetstream Team has users() relationship, keep alias for clarity
        return $this->users();
    }

    /**
     * Safe country label (no Symfony Intl). Uses config/countries.php.
     */
    public function getHqCountryLabelAttribute(): string
    {
        $code = (string) ($this->hq_country ?? '');

        if ($code === '') {
            return '—';
        }

        $map = collect(config('countries', []))
            ->filter(fn ($c) => isset($c['value'], $c['label']))
            ->mapWithKeys(fn ($c) => [$c['value'] => $c['label']]);

        return $map->get($code, $code);
    }
}
