<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Enums\CompStructure;
use App\Enums\OpeningType;
use App\Enums\ScreeningPolicy;

class Opening extends Model
{
    /**
     * Mass-assignable attributes
     */
    protected $fillable = [
        'team_id',

        // Core
        'title',
        'slug',
        'description',
        'company_type',     // 'manufacturer' | 'distributor'
        'status',           // 'draft' | 'published' | 'archived'
        'specialty_ids',
        'territory_ids',
        'compensation',
        'visibility_until',

        // Optional enums
        'comp_structure',   // CompStructure enum cast
        'opening_type',     // OpeningType enum cast

        // Roleplay
        'roleplay_policy',              // 'disabled' | 'optional' | 'required'
        'roleplay_scenario_pack_id',
        'roleplay_pass_threshold',

        // Screening
        'screening_policy', // ScreeningPolicy enum cast
        'screening_rules',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'specialty_ids'      => 'array',
        'territory_ids'      => 'array',
        'visibility_until'   => 'date',
        'roleplay_pass_threshold' => 'decimal:2',

        'comp_structure'     => CompStructure::class,
        'opening_type'       => OpeningType::class,
        'screening_policy'   => ScreeningPolicy::class,

        'screening_rules'    => 'array',
    ];

    // Relationships
    public function team()
    {
        return $this->belongsTo(\App\Models\Team::class);
    }

    public function applications()
    {
        return $this->hasMany(\App\Models\Application::class);
    }

    public function scenarioPack()
    {
        return $this->belongsTo(\App\Models\RoleplayScenarioPack::class, 'roleplay_scenario_pack_id');
    }
}
