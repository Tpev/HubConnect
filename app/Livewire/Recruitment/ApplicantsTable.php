<?php

namespace App\Livewire\Recruitment;

use App\Models\Application;
use App\Models\Opening;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class ApplicantsTable extends Component
{
    use WithPagination;

    public Opening $opening;

    #[Url(as: 'q')]
    public string $search = '';

    // Nullable to avoid Livewire clearable->null crash
    #[Url]
    public ?string $status = 'all'; // all|new|shortlisted|rejected|hired

    #[Url]
    public ?string $sort = 'newest'; // newest|name|score

    #[Url]
    public ?int $perPage = 10;

    public ?int $selectedId = null;

    // TallStack select options
    public array $statusOptions = [
        ['label' => 'All statuses', 'value' => 'all'],
        ['label' => 'New',          'value' => 'new'],
        ['label' => 'Shortlisted',  'value' => 'shortlisted'],
        ['label' => 'Rejected',     'value' => 'rejected'],
        ['label' => 'Hired',        'value' => 'hired'],
    ];

    public array $sortOptions = [
        ['label' => 'Newest',           'value' => 'newest'],
        ['label' => 'Name (A→Z)',       'value' => 'name'],
        ['label' => 'Score (high→low)', 'value' => 'score'],
    ];

    public array $perPageOptions = [
        ['label' => '10', 'value' => 10],
        ['label' => '25', 'value' => 25],
        ['label' => '50', 'value' => 50],
    ];

    public function mount(Opening $opening): void
    {
        /** @var Authenticatable&\App\Models\User $user */
        $user   = Auth::user();
        $teamId = $user?->currentTeam?->id;

        abort_unless($opening->team_id === $teamId, 403);
        $this->opening = $opening;

        // Coerce defaults if cleared by querystring or UI
        $this->status  = $this->status  ?: 'all';
        $this->sort    = $this->sort    ?: 'newest';
        $this->perPage = $this->perPage ?: 10;
    }

    /* Filters reset pagination */
    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingSort(): void   { $this->resetPage(); }
    public function updatingPerPage(): void{ $this->resetPage(); }

    /* Coerce nulls from clear buttons */
    public function updatedStatus($value): void  { $this->status  = $value ?: 'all'; }
    public function updatedSort($value): void    { $this->sort    = $value ?: 'newest'; }
    public function updatedPerPage($value): void { $this->perPage = (int) ($value ?: 10); }

    /* Row actions (quick status changes) */
    public function shortlist(int $id): void
    {
        $app = $this->findForTeam($id);
        $app->update(['status' => 'shortlisted']);
        $this->dispatch('toast', type: 'success', message: 'Applicant shortlisted.');
    }

    public function reject(int $id): void
    {
        $app = $this->findForTeam($id);
        $app->update(['status' => 'rejected']);
        $this->dispatch('toast', type: 'success', message: 'Applicant rejected.');
    }

    public function hire(int $id): void
    {
        $app = $this->findForTeam($id);
        $app->update(['status' => 'hired']);
        $this->dispatch('toast', type: 'success', message: 'Applicant marked as hired.');
    }

    /* Drawer control */
    public function openDrawer(int $id): void
    {
        $this->selectedId = $id; // Conditional render mounts the drawer
    }

    #[On('applicant-drawer:closed')]
    public function onDrawerClosed(): void
    {
        $this->selectedId = null; // Unmount drawer
    }

    /* Query builder */
    protected function query()
    {
        /** @var Authenticatable&\App\Models\User $user */
        $user   = Auth::user();
        $teamId = $user?->currentTeam?->id;

        $q = Application::query()
            ->where('team_id', $teamId)
            ->where('opening_id', $this->opening->id);

        if ($this->search !== '') {
            $like = '%' . trim($this->search) . '%';
            $q->where(function ($qq) use ($like) {
                $qq->where('candidate_name', 'like', $like)
                   ->orWhere('email', 'like', $like)
                   ->orWhere('phone', 'like', $like)
                   ->orWhere('location', 'like', $like);
            });
        }

        if (($this->status ?? 'all') !== 'all') {
            $q->where('status', $this->status);
        }

        $sort = $this->sort ?: 'newest';

        $q->when($sort === 'newest', fn($qq) => $qq->orderByDesc('created_at'))
          ->when($sort === 'name',   fn($qq) => $qq->orderBy('candidate_name'))
          ->when($sort === 'score',  fn($qq) => $qq->orderByDesc('score'));

        return $q;
    }

    protected function findForTeam(int $id): Application
    {
        /** @var Authenticatable&\App\Models\User $user */
        $user   = Auth::user();
        $teamId = $user?->currentTeam?->id;

        return Application::where('team_id', $teamId)
            ->where('opening_id', $this->opening->id)
            ->findOrFail($id);
    }

    public function render()
    {
        $apps = $this->query()->paginate($this->perPage ?: 10);

        return view('livewire.recruitment.applicants-table', [
            'applications'     => $apps,
            'opening'          => $this->opening,
            'statusOptions'    => $this->statusOptions,
            'sortOptions'      => $this->sortOptions,
            'perPageOptions'   => $this->perPageOptions,
        ]);
    }
}
