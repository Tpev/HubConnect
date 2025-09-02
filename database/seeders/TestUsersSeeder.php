<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Team;
use Illuminate\Support\Facades\Hash;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        // Manufacturer — ACTIVE
        $m1 = User::updateOrCreate(
            ['email' => 'manu.active@example.com'],
            ['name' => 'Manufacturer Active', 'password' => Hash::make('password')]
        );

        $m1Team = Team::updateOrCreate(
            ['name' => 'Manufacturer Active Team'],
            ['user_id' => $m1->id, 'personal_team' => false, 'license_active' => true]
        );

        // attach user to team (owner role) and set as current team
        $m1->teams()->syncWithoutDetaching([$m1Team->id => ['role' => 'owner']]);
        $m1->forceFill(['current_team_id' => $m1Team->id])->save();

        // Manufacturer — INACTIVE
        $m2 = User::updateOrCreate(
            ['email' => 'manu.inactive@example.com'],
            ['name' => 'Manufacturer Inactive', 'password' => Hash::make('password')]
        );

        $m2Team = Team::updateOrCreate(
            ['name' => 'Manufacturer Inactive Team'],
            ['user_id' => $m2->id, 'personal_team' => false, 'license_active' => false]
        );

        $m2->teams()->syncWithoutDetaching([$m2Team->id => ['role' => 'owner']]);
        $m2->forceFill(['current_team_id' => $m2Team->id])->save();

        // Distributor — ACTIVE
        $d1 = User::updateOrCreate(
            ['email' => 'distri.active@example.com'],
            ['name' => 'Distributor Active', 'password' => Hash::make('password')]
        );

        $d1Team = Team::updateOrCreate(
            ['name' => 'Distributor Active Team'],
            ['user_id' => $d1->id, 'personal_team' => false, 'license_active' => true]
        );

        $d1->teams()->syncWithoutDetaching([$d1Team->id => ['role' => 'owner']]);
        $d1->forceFill(['current_team_id' => $d1Team->id])->save();

        // Distributor — INACTIVE
        $d2 = User::updateOrCreate(
            ['email' => 'distri.inactive@example.com'],
            ['name' => 'Distributor Inactive', 'password' => Hash::make('password')]
        );

        $d2Team = Team::updateOrCreate(
            ['name' => 'Distributor Inactive Team'],
            ['user_id' => $d2->id, 'personal_team' => false, 'license_active' => false]
        );

        $d2->teams()->syncWithoutDetaching([$d2Team->id => ['role' => 'owner']]);
        $d2->forceFill(['current_team_id' => $d2Team->id])->save();
    }
}
