<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Certification;

class CertificationSeeder extends Seeder
{
    public function run(): void
    {
        $data = [
            ['code'=>'FDA_510K','name'=>'FDA 510(k)'],
            ['code'=>'CE_MDR','name'=>'CE (MDR)'],
            ['code'=>'ISO_13485','name'=>'ISO 13485'],
            ['code'=>'ANVISA','name'=>'ANVISA (Brazil)'],
        ];

        foreach ($data as $row) {
            Certification::updateOrCreate(['code'=>$row['code']], $row);
        }
    }
}
