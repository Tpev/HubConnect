<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            SpecialtySeeder::class,
            TerritorySeeder::class,
			DeviceCategorySeeder::class,
			TestUsersSeeder::class,
			CertificationSeeder::class,
			SpecialtySeeder::class,
			RoleplayScenarioPackSeeder::class,
        ]);
    }
}
