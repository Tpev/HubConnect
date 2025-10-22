<?php

namespace App\Livewire\Geo;

use Livewire\Component;
use Livewire\Attributes\Modelable;
use App\Services\Geo\GeoResolverService;

class LocationOmnibox extends Component
{
    public string $query = '';

    #[Modelable]
    public ?array $chips = [];   // <-- allow null

    public ?string $biasCountryIso2 = null;
    public int $limit = 8;

    public array $suggestions = [
        'countries' => [],
        'states'    => [],
        'cities'    => [],
        'provider'  => [
            'countries' => [],
            'states'    => [],
            'cities'    => [],
        ],
    ];

    public function mount($value = [], $biasCountryIso2 = null): void
    {
        // Coerce anything truthy to array; else []
        $this->chips = is_array($value) ? $value : [];
        $this->biasCountryIso2 = $biasCountryIso2;
    }

    // Livewire will call this when wire:model updates the property
    public function updatedChips($value): void
    {
        $this->chips = is_array($value) ? $value : [];
    }

    public function updatedQuery(): void
    {
        $svc = app(GeoResolverService::class);
        $this->suggestions = $svc->suggest($this->query, $this->limit, $this->biasCountryIso2);
    }

    public function pick(array $item): void
    {
        $svc  = app(GeoResolverService::class);
        $norm = $svc->normalizeSelection($item);

        $chips = $this->chips ?? []; // null-safe
        foreach ($chips as $c) {
            if ($c['type'] === $norm['type'] && (string)$c['id'] === (string)$norm['id']) {
                $this->resetQueryAndSuggestions();
                return;
            }
        }

        $chips[]      = $norm;
        $this->chips  = $chips; // assign coerced array
        $this->resetQueryAndSuggestions();
    }

    public function removeChip(int $index): void
    {
        $chips = $this->chips ?? [];
        if (isset($chips[$index])) {
            array_splice($chips, $index, 1);
        }
        $this->chips = $chips;
    }

    public function render()
    {
        return view('livewire.geo.location-omnibox');
    }

    protected function resetQueryAndSuggestions(): void
    {
        $this->query = '';
        $this->suggestions = [
            'countries' => [],
            'states'    => [],
            'cities'    => [],
            'provider'  => [
                'countries' => [],
                'states'    => [],
                'cities'    => [],
            ],
        ];
    }
}
