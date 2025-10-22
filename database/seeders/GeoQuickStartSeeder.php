<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\GeoCountry;
use App\Models\GeoSubdivision;

class GeoQuickStartSeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['iso2'=>'US','iso3'=>'USA','name'=>'United States'],
            ['iso2'=>'FR','iso3'=>'FRA','name'=>'France'],
        ];
        foreach ($countries as $c) GeoCountry::firstOrCreate(['iso2'=>$c['iso2']], $c);

        $us = GeoCountry::where('iso2','US')->first();
        $fr = GeoCountry::where('iso2','FR')->first();

        $states = [
            ['country_id'=>$us->id,'iso_3166_2'=>'US-CA','name'=>'California'],
            ['country_id'=>$us->id,'iso_3166_2'=>'US-TX','name'=>'Texas'],
            ['country_id'=>$us->id,'iso_3166_2'=>'US-NY','name'=>'New York'],
            ['country_id'=>$fr->id,'iso_3166_2'=>'FR-IDF','name'=>'Île-de-France'],
            ['country_id'=>$fr->id,'iso_3166_2'=>'FR-ARA','name'=>'Auvergne-Rhône-Alpes'],
            ['country_id'=>$fr->id,'iso_3166_2'=>'FR-PAC',"name"=>"Provence-Alpes-Côte d'Azur"],
        ];
        foreach ($states as $s) GeoSubdivision::firstOrCreate(['iso_3166_2'=>$s['iso_3166_2']], $s);
    }
}
