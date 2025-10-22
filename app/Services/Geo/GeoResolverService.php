<?php
namespace App\Services\Geo;

use App\Repositories\Geo\GeoRepository;
use App\Services\Geo\Providers\GooglePlacesProvider;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class GeoResolverService
{
    public function __construct(
        protected GeoRepository $repo,
        protected GooglePlacesProvider $google
    ) {}

    public function suggest(string $q, int $limit = 8, ?string $biasIso2 = null): array
    {
        // Local first
        $local = $this->repo->searchLocal($q, $biasIso2, $limit);

        // Provider
        $prov = $this->google->autocomplete(
            $q,
            $biasIso2 ?: config('geo.bias_country'),
            config('geo.locale'),
            $limit
        );

        // Group provider results by type for UI
        $buckets = ['countries'=>[], 'states'=>[], 'cities'=>[]];
        foreach ($prov as $item) {
            if ($item['type'] === 'country') $buckets['countries'][] = $item;
            elseif ($item['type'] === 'state') $buckets['states'][] = $item;
            elseif ($item['type'] === 'city') $buckets['cities'][] = $item;
        }

        return [
            'countries' => $local['countries'],
            'states'    => $local['states'],
            'cities'    => $local['cities'],
            'provider'  => $buckets,
        ];
    }

    /**
     * Normalize a selected item (local or provider) into canonical DB entities.
     * Returns a chip: ['type'=>'country|state|city','id'=>int,'label'=>string]
     */
    public function normalizeSelection(array $item): array
    {
        if (!empty($item['id']) && is_numeric($item['id'])) {
            // Already local record (country/state/city)
            return $item;
        }

        // Provider selection → fetch details
        $placeId = Str::after((string)($item['id'] ?? ''), 'prov:');
        $det = $this->google->details($placeId, config('geo.locale'));
        if (!$det) return $item;

        $components = $det['address_components'] ?? [];
        $loc        = $det['location'] ?? null;
        $name       = $det['name'] ?? null;

        // Parse components into ISO-ish fields
        $countryIso2 = self::componentShort($components, 'country'); // e.g., US, FR
        $stateShort  = self::componentShort($components, 'administrative_area_level_1'); // e.g., CA, TX, IDF
        $stateName   = self::componentLong($components, 'administrative_area_level_1');  // e.g., California
        $cityName    = self::componentLong($components, 'locality') ?? $name;            // fallback to place name

        // Country
        $country = $this->repo->firstOrCreateCountry($countryIso2, self::componentLong($components, 'country') ?: $countryIso2);

        // State
        $state = null;
        if ($stateShort) {
            $iso3166_2 = strtoupper($countryIso2).'-'.strtoupper($stateShort); // US-CA, FR-IDF
            $state = $this->repo->firstOrCreateState($iso3166_2, $country->id, $stateName ?: $iso3166_2);
        }

        // Branch by selected type
        if ($item['type'] === 'country') {
            return ['type'=>'country','id'=>$country->id,'label'=>$country->name.' ('.$country->iso2.')'];
        }

        if ($item['type'] === 'state') {
            $label = ($state?->name ?? $stateName ?? $stateShort). ' — ' . ($state?->iso_3166_2 ?? (strtoupper($countryIso2).'-'.strtoupper($stateShort)));
            return ['type'=>'state','id'=>$state->id,'label'=>$label];
        }

        // City
        $place = $this->repo->upsertCity([
            'country_id'     => $country->id,
            'subdivision_id' => $state?->id,
            'ext_source'     => 'google',
            'ext_id'         => $placeId,
            'name'           => $cityName ?: ($name ?: 'City'),
            'ascii_name'     => null,
            'lat'            => $loc['lat'] ?? null,
            'lng'            => $loc['lng'] ?? null,
            'captured_at'    => now(),
        ]);

        $right = trim(($state?->name ? $state->name.', ' : '').$country->iso2);
        return ['type'=>'city','id'=>$place->id,'label'=>$place->name.($right ? " — {$right}" : '')];
    }

    protected static function componentShort(array $components, string $type): ?string
    {
        $c = collect($components)->first(fn($x) => in_array($type, $x['types'] ?? []));
        return $c['short_name'] ?? null;
    }

    protected static function componentLong(array $components, string $type): ?string
    {
        $c = collect($components)->first(fn($x) => in_array($type, $x['types'] ?? []));
        return $c['long_name'] ?? null;
    }
}
