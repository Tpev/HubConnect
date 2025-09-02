<?php

namespace App\Livewire\Manufacturer;

use App\Models\Device;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Database\Eloquent\Builder;

class DeviceIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public ?int $quantity = 10;
    public string $status = 'all';

    protected $queryString = [
        'search'   => ['except' => ''],
        'quantity' => ['except' => 10],
        'status'   => ['except' => 'all'],
        'page'     => ['except' => 1],
    ];

    public function updatedSearch()   { $this->resetPage(); }
    public function updatedQuantity() { $this->resetPage(); }
    public function updatedStatus()   { $this->resetPage(); }

    /** TallStackUI v2 table expects 'index' key */
    public function getHeaders(): array
    {
        return [
            ['index' => 'name',          'label' => 'Name',        'sortable' => true],
            ['index' => 'category',      'label' => 'Category',    'sortable' => false],
            ['index' => 'margin_target', 'label' => 'Margin %',    'sortable' => true],
            ['index' => 'territories',   'label' => 'Territories', 'sortable' => false],
            ['index' => 'status',        'label' => 'Status',      'sortable' => true],
            ['index' => 'actions',       'label' => '',            'sortable' => false],
        ];
    }

    /** Full US name -> 2-letter code (plus DC) */
    private const STATE_NAME_TO_CODE = [
        'alabama'=>'AL','alaska'=>'AK','arizona'=>'AZ','arkansas'=>'AR','california'=>'CA','colorado'=>'CO','connecticut'=>'CT','delaware'=>'DE','district of columbia'=>'DC',
        'florida'=>'FL','georgia'=>'GA','hawaii'=>'HI','idaho'=>'ID','illinois'=>'IL','indiana'=>'IN','iowa'=>'IA','kansas'=>'KS','kentucky'=>'KY',
        'louisiana'=>'LA','maine'=>'ME','maryland'=>'MD','massachusetts'=>'MA','michigan'=>'MI','minnesota'=>'MN','mississippi'=>'MS','missouri'=>'MO','montana'=>'MT',
        'nebraska'=>'NE','nevada'=>'NV','new hampshire'=>'NH','new jersey'=>'NJ','new mexico'=>'NM','new york'=>'NY','north carolina'=>'NC','north dakota'=>'ND','ohio'=>'OH',
        'oklahoma'=>'OK','oregon'=>'OR','pennsylvania'=>'PA','rhode island'=>'RI','south carolina'=>'SC','south dakota'=>'SD','tennessee'=>'TN','texas'=>'TX','utah'=>'UT',
        'vermont'=>'VT','virginia'=>'VA','washington'=>'WA','west virginia'=>'WV','wisconsin'=>'WI','wyoming'=>'WY',
    ];

    /** Normalize territory->name into a 2-letter code */
    private function territoryNameToCode(?string $name): ?string
    {
        if (!$name) return null;

        $trim = trim($name);

        // If it's already a 2-letter code
        if (strlen($trim) === 2 && ctype_alpha($trim)) {
            return strtoupper($trim);
        }

        // Map full name -> code
        $key = strtolower($trim);
        return self::STATE_NAME_TO_CODE[$key] ?? null;
    }

    public function render()
    {
        $headers = $this->getHeaders();

        $rows = Device::query()
            ->with([
                'category:id,name',
                'territories:id,name', // <-- only select existing columns
            ])
            ->when($this->status !== 'all', function (Builder $q) {
                $q->where('status', $this->status);
            })
            ->when(filled($this->search), function (Builder $q) {
                $term = '%' . str_replace(' ', '%', $this->search) . '%';

                $q->where(function (Builder $i) use ($term) {
                    $i->where('name', 'like', $term)
                      ->orWhere('slug', 'like', $term)
                      ->orWhere('description', 'like', $term)
                      ->orWhere('indications', 'like', $term);
                })
                ->orWhereHas('category', function (Builder $c) use ($term) {
                    $c->where('name', 'like', $term);
                })
                ->orWhereHas('territories', function (Builder $t) use ($term) {
                    $t->where('name', 'like', $term);
                });
            })
            ->orderByDesc('id')
            ->paginate($this->quantity ?? 10)
            ->withQueryString();

        // Build the state code list for the MAP from the CURRENT PAGE
        $targetStates = collect($rows->items())
            ->flatMap(fn ($d) => $d->territories->pluck('name'))
            ->map(fn ($n) => $this->territoryNameToCode($n))
            ->filter()
            ->unique()
            ->values()
            ->all();

        return view('livewire.manufacturer.device-index', compact('headers', 'rows', 'targetStates'));
    }
}
