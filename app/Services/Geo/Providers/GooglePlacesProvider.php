<?php
namespace App\Services\Geo\Providers;

use Illuminate\Support\Facades\Http;

class GooglePlacesProvider
{
    protected string $key;
    protected string $autoUrl;
    protected string $detailsUrl;
    protected array  $accepted;

    public function __construct()
    {
        $this->key        = (string) config('geo.google.key');
        $this->autoUrl    = (string) config('geo.google.autocomplete_url');
        $this->detailsUrl = (string) config('geo.google.details_url');
        $this->accepted   = (array)  config('geo.google.accepted_types');
    }

    public function autocomplete(string $input, ?string $biasIso2 = null, string $locale = 'en', int $limit = 8): array
    {
        if ($input === '') return [];

        $http = Http::timeout(8)->withOptions([
            // Prefer a CA bundle if provided, else default verification
            'verify' => $this->verifyOption(),
        ]);

        $params = [
            'key'      => $this->key,
            'input'    => $input,
            'language' => $locale,
            // NOTE: we bias locally; you can add 'components' => "country:{$biasIso2}" to restrict if desired
        ];

        $resp = $http->get($this->autoUrl, array_filter($params));
        if (!$resp->ok()) return [];

        $preds = $resp->json('predictions', []);
        $out = [];

        foreach ($preds as $p) {
            $types = $p['types'] ?? [];
            $placeId = $p['place_id'] ?? null;
            $desc = $p['description'] ?? '';

            if (!array_intersect($types, $this->accepted)) continue;

            $type = in_array('country', $types) ? 'country'
                  : (in_array('administrative_area_level_1', $types) ? 'state'
                  : (in_array('locality', $types) ? 'city' : null));

            if (!$type) continue;

            $out[] = [
                'provider' => 'google',
                'type'     => $type,
                'id'       => "prov:$placeId",
                'label'    => $desc,
                'place_id' => $placeId,
                'raw'      => $p,
            ];
            if (count($out) >= $limit) break;
        }

        return $out;
    }

    public function details(string $placeId, string $locale = 'en'): ?array
    {
        $http = Http::timeout(8)->withOptions([
            'verify' => $this->verifyOption(),
        ]);

        $params = [
            'key'      => $this->key,
            'place_id' => $placeId,
            'language' => $locale,
            'fields'   => config('geo.google.details_fields'),
        ];
        $resp = $http->get($this->detailsUrl, $params);
        if (!$resp->ok()) return null;

        $r = $resp->json('result');

        return [
            'address_components' => $r['address_components'] ?? [],
            'location' => $r['geometry']['location'] ?? null, // ['lat'=>..,'lng'=>..]
            'name'     => $r['name'] ?? null,
        ];
    }

    /**
     * Decide how Guzzle verifies TLS:
     * - If GEO_DISABLE_SSL_VERIFY=true => disable (dev-only!)
     * - Else if GEO_CA_BUNDLE points to a file => use that bundle
     * - Else => default (true)
     */
    protected function verifyOption(): bool|string
    {
        if (filter_var(env('GEO_DISABLE_SSL_VERIFY', false), FILTER_VALIDATE_BOOL)) {
            return false; // DEV ONLY – do not enable in prod
        }

        $bundle = env('GEO_CA_BUNDLE');
        if ($bundle && is_file($bundle)) {
            return $bundle; // path to cacert.pem
        }

        // Try a sensible project default: storage/certs/cacert.pem
        $projectBundle = storage_path('certs/cacert.pem');
        if (is_file($projectBundle)) {
            return $projectBundle;
        }

        return true; // default verification
    }
}
