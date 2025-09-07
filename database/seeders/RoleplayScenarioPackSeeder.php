<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class RoleplayScenarioPackSeeder extends Seeder
{
public function run(): void
{
    \App\Models\RoleplayScenarioPack::updateOrCreate(
        ['name' => 'Spine Ortho — Distributor Rep'],
        [
            'description' => 'Cold-open + objection handling + pricing pressure.',
            'config' => [
                'scenarios' => ['cold_open','needs_discovery','pricing_pressure'],
                'weights' => ['discovery'=>30,'qualification'=>20,'objections'=>30,'close'=>20],
            ],
        ]
    );
}

}
