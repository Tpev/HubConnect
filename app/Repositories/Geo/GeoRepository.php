<?php
namespace App\Repositories\Geo;

use App\Models\GeoCountry;
use App\Models\GeoSubdivision;
use App\Models\GeoPlace;

class GeoRepository
{
    public function searchLocal(string $q, ?string $biasIso2 = null, int $limit = 8): array
    {
        $q = trim($q);
        if ($q === '') return ['countries'=>[], 'states'=>[], 'cities'=>[]];

        $countries = GeoCountry::query()
            ->when($biasIso2, fn($qq) => $qq->orderByRaw("iso2 = ? desc", [$biasIso2]))
            ->where(fn($x) => $x
                ->where('name','like',"%$q%")
                ->orWhere('iso2','like',"%$q%")
                ->orWhere('iso3','like',"%$q%"))
            ->limit($limit)->get()
            ->map(fn($c) => [
                'type'=>'country','id'=>$c->id,
                'label'=> "{$c->name} ({$c->iso2})",
                'meta'=> ['iso2'=>$c->iso2],
            ]);

        $states = GeoSubdivision::query()
            ->where(fn($x) => $x
                ->where('name','like',"%$q%")
                ->orWhere('iso_3166_2','like',"%$q%"))
            ->with('country:id,iso2,name')
            ->limit($limit)->get()
            ->map(fn($s) => [
                'type'=>'state','id'=>$s->id,
                'label'=> "{$s->name} — {$s->iso_3166_2}",
                'meta'=> ['iso_code'=>$s->iso_3166_2, 'country'=>$s->country?->iso2],
            ]);

        $cities = GeoPlace::query()
            ->where('name','like',"%$q%")
            ->with(['country:id,iso2,name','subdivision:id,iso_3166_2,name'])
            ->limit($limit)->get()
            ->map(function($p){
                $right = trim(($p->subdivision?->name ? $p->subdivision->name.', ' : '').($p->country?->iso2 ?? ''));
                return [
                    'type'=>'city','id'=>$p->id,
                    'label'=> $p->name.($right ? " — {$right}" : ''),
                    'meta'=> [
                        'country'=>$p->country?->iso2,
                        'state'=>$p->subdivision?->iso_3166_2,
                        'lat'=>$p->lat, 'lng'=>$p->lng
                    ],
                ];
            });

        return [
            'countries'=>$countries->values()->all(),
            'states'=>$states->values()->all(),
            'cities'=>$cities->values()->all(),
        ];
    }

    public function getCountryByIso2(string $iso2): ?GeoCountry
    {
        return GeoCountry::where('iso2', strtoupper($iso2))->first();
    }

    public function firstOrCreateCountry(string $iso2, ?string $name = null): GeoCountry
    {
        return GeoCountry::firstOrCreate(['iso2'=>strtoupper($iso2)], [
            'name' => $name ?: strtoupper($iso2),
            'iso3' => null,
        ]);
    }

    public function firstOrCreateState(string $iso3166_2, int $countryId, ?string $name = null): GeoSubdivision
    {
        return GeoSubdivision::firstOrCreate(['iso_3166_2'=>strtoupper($iso3166_2)], [
            'country_id' => $countryId,
            'name' => $name ?: strtoupper($iso3166_2),
        ]);
    }

    public function upsertCity(array $data): GeoPlace
    {
        if (!empty($data['ext_source']) && !empty($data['ext_id'])) {
            $p = GeoPlace::firstOrNew([
                'ext_source' => $data['ext_source'],
                'ext_id'     => $data['ext_id'],
            ]);
        } else {
            $p = GeoPlace::firstOrNew([
                'name' => $data['name'],
                'country_id' => $data['country_id'],
                'subdivision_id' => $data['subdivision_id'] ?? null,
            ]);
        }
        $p->fill($data);
        $p->save();
        return $p;
    }
}
