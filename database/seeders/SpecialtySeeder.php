<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code'=>'orthopedics','name'=>'Orthopedics','parent_code'=>null],
            ['code'=>'spine','name'=>'Spine','parent_code'=>'orthopedics'],
            ['code'=>'trauma','name'=>'Trauma','parent_code'=>'orthopedics'],
            ['code'=>'wound_care','name'=>'Wound Care','parent_code'=>null],
            ['code'=>'cardiology','name'=>'Cardiology','parent_code'=>null],
            ['code'=>'diagnostics','name'=>'Diagnostics','parent_code'=>null],
        ];

        foreach ($data as $row) {
            Specialty::updateOrCreate(['code'=>$row['code']], $row);
        }
    }
}
