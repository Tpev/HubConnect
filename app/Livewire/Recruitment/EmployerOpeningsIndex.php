<?php

namespace App\Livewire\Recruitment;

use App\Models\Opening;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')] // adjust to your app layout
class EmployerOpeningsIndex extends Component
{
    use WithPagination;

    #[Url(as: 'q')]
    public string $search = '';

    #[Url]
    public string $status = 'all'; // all|draft|published|archived

    #[Url]
    public string $sort = 'newest'; // newest|title|visibility

    #[Url]
    public int $perPage = 10;

    public function mount(): void
    {
        // Feature flag guard
        if (! config('features.recruitment')) {
            abort(403, 'Recruitment feature is disabled.');
        }
    }

    public function updatingSearch(): void { $this->resetPage(); }
    public function updatingStatus(): void { $this->resetPage(); }
    public function updatingSort(): void   { $this->resetPage(); }
    public function updatingPerPage(): void{ $this->resetPage(); }

    public function publish(int $openingId): void
    {
        $opening = $this->openingForTeam($openingId);
        $opening->update(['status' => 'published']);
        $this->dispatch('toast', type: 'success', message: 'Opening published.');
    }

    public function archive(int $openingId): void
    {
        $opening = $this->openingForTeam($openingId);
        $opening->update(['status' => 'archived']);
        $this->dispatch('toast', type: 'success', message: 'Opening archived.');
    }

    public function duplicate(int $openingId): void
    {
        $opening = $this->openingForTeam($openingId);
        $copy = $opening->replicate(['slug', 'created_at', 'updated_at']);
        $copy->slug = \Str::slug($opening->title) . '-' . \Str::random(6);
        $copy->status = 'draft';
        $copy->push();
        $this->dispatch('toast', type: 'success', message: 'Opening duplicated as draft.');
    }

    protected function openingForTeam(int $id): Opening
    {
        /** @var Authenticatable&\App\Models\User $user */
        $user = Auth::user();
        $teamId = $user?->currentTeam?->id;

        return Opening::where('team_id', $teamId)->findOrFail($id);
    }

    protected function query()
    {
        /** @var Authenticatable&\App\Models\User $user */
        $user = Auth::user();
        $teamId = $user?->currentTeam?->id;

        $q = Opening::query()
            ->where('team_id', $teamId);

        if ($this->search !== '') {
            $like = '%' . trim($this->search) . '%';
            $q->where(function ($qq) use ($like) {
                $qq->where('title', 'like', $like)
                   ->orWhere('description', 'like', $like)
                   ->orWhere('compensation', 'like', $like);
            });
        }

        if ($this->status !== 'all') {
            $q->where('status', $this->status);
        }

        // Sorting
        $q->when($this->sort === 'newest', fn($qq) => $qq->orderByDesc('created_at'))
          ->when($this->sort === 'title', fn($qq) => $qq->orderBy('title'))
          ->when($this->sort === 'visibility', fn($qq) => $qq->orderByDesc('visibility_until'));

        return $q;
    }

    public function render()
    {
        $openings = $this->query()->paginate($this->perPage);

        return view('livewire.recruitment.employer-openings-index', [
            'openings' => $openings,
        ]);
    }
}
