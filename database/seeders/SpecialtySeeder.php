<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Specialty;

class SpecialtySeeder extends Seeder
{
    public function run(): void
    {
        $names = [
            'Cardiology','Dermatology','Endocrinology','Gastroenterology','Hematology',
            'Infectious Disease','Nephrology','Neurology','Neurosurgery','OB/GYN',
            'Oncology','Ophthalmology','Orthopedics','Otolaryngology (ENT)','Pediatrics',
            'Plastic Surgery','Primary Care / Family Medicine','Pulmonology','Radiology',
            'Rheumatology','Urology','Vascular Surgery','Wound Care',
        ];

        foreach ($names as $n) {
            Specialty::firstOrCreate(['name' => $n]);
        }
    }
}
