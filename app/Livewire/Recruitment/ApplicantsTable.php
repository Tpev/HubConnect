<?php

namespace App\Livewire\Recruitment;

use App\Models\Application;
use App\Models\Opening;
use Carbon\Carbon;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
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

    // Nullable to avoid clearable->null issues
    #[Url] public ?string $status  = 'all';   // all|new|shortlisted|rejected|hired
    #[Url] public ?string $sort    = 'newest';// newest|name|score
    #[Url] public ?int    $perPage = 10;

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

    /* Row actions */
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

    public function sendInvite(int $id): void
    {
        $app = $this->findForTeam($id);
        if ($app->invite_token) {
            $this->dispatch('toast', type: 'info', message: 'Invite already sent.');
            return;
        }
        $app->invite_token = Str::random(32);
        $app->invited_at   = now();
        $app->save();

        $this->dispatch('toast', type: 'success', message: 'Roleplay invite sent.');
    }

    public function regenerateInvite(int $id): void
    {
        $app = $this->findForTeam($id);
        $app->invite_token = Str::random(32);
        $app->invited_at   = now();
        $app->save();

        $this->dispatch('toast', type: 'success', message: 'Invite regenerated.');
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

    public function render()
    {
        $apps = $this->query()->paginate($this->perPage ?: 10);

        // Prepare opening rules/policy once
        [$policy, $rules, $hash] = $this->openingRulesBundle($this->opening);

        // Evaluate for each visible row and sync DB if needed
        foreach ($apps as $app) {
            $answers = $this->answersFromApp($app);
            [$verdict, $failCount, $flagCount] = $this->evaluate($rules, $policy, $answers);

            $needsSync = ($app->screening_rules_hash !== $hash)
                      || ($app->screening_verdict !== $verdict)
                      || ((int)$app->screening_fail_count !== (int)$failCount)
                      || ((int)$app->screening_flag_count !== (int)$flagCount)
                      || empty($app->screening_answers);

            if ($needsSync) {
                $app->screening_verdict    = $verdict;
                $app->screening_fail_count = $failCount;
                $app->screening_flag_count = $flagCount;
                $app->screening_rules_hash = $hash;
                // snapshot answers (JSON) — keep small, only fields we evaluate on
                $app->screening_answers    = json_encode($answers);
                // auto-reject only if HARD policy & failing (do NOT flip existing shortlisted/hired)
                if ($verdict === 'hard_block' && $app->status === 'new') {
                    $app->status = 'rejected';
                    $app->auto_rejected_at = now();
                }
                $app->save();
            }

            // attach computed block for the Blade to consume
            $app->computed_screening = [
                'verdict'    => $verdict,
                'fail_count' => $failCount,
                'flag_count' => $flagCount,
                'synced'     => !$needsSync,
            ];
        }

        return view('livewire.recruitment.applicants-table', [
            'applications'   => $apps,
            'opening'        => $this->opening,
            'statusOptions'  => $this->statusOptions,
            'sortOptions'    => $this->sortOptions,
            'perPageOptions' => $this->perPageOptions,
        ]);
    }

    /* ==================== Helpers ==================== */

    protected function findForTeam(int $id): Application
    {
        /** @var Authenticatable&\App\Models\User $user */
        $user   = Auth::user();
        $teamId = $user?->currentTeam?->id;

        return Application::where('team_id', $teamId)
            ->where('opening_id', $this->opening->id)
            ->findOrFail($id);
    }

    /**
     * Return [policy, rules[], hash]
     */
    protected function openingRulesBundle(Opening $opening): array
    {
        $policyRaw = $opening->screening_policy ?? 'off';
        $policy = $policyRaw instanceof \BackedEnum
            ? $policyRaw->value
            : (is_string($policyRaw) ? $policyRaw : 'off');

        $rawRules = $opening->screening_rules ?? [];
        $rules = is_string($rawRules)
            ? (json_decode($rawRules, true) ?: [])
            : (is_array($rawRules) ? $rawRules : collect($rawRules)->toArray());

        // Normalize for hashing
        $norm = collect($rules)
            ->map(fn($r) => Arr::only($r, ['field','op','value','min','max','severity']))
            ->values()
            ->all();

        $hash = sha1($policy . '|' . json_encode($norm));

        return [$policy, $rules, $hash];
    }

    /**
     * Build candidate answers from columns/snapshot.
     */
    protected function answersFromApp(Application $app): array
    {
        // prefer snapshot (JSON)
        if (is_array($app->screening_answers)) {
            return $app->screening_answers;
        }
        if (is_string($app->screening_answers) && $app->screening_answers !== '') {
            $decoded = json_decode($app->screening_answers, true);
            if (is_array($decoded)) return $decoded;
        }

        // else compose from columns (handle JSON strings as arrays)
        $toArray = function ($v): array {
            if (is_array($v)) return $v;
            if (is_string($v) && strlen($v) && ($v[0] === '[' || $v[0] === '{')) {
                return json_decode($v, true) ?: [];
            }
            return $v ? (array) $v : [];
        };

        return [
            'years_total'              => $this->toNumber($app->years_total),
            'years_med_device'         => $this->toNumber($app->years_med_device),
            'specialties'              => $toArray($app->candidate_specialties),
            'state'                    => $app->state,
            'travel_percent_max'       => $this->toNumber($app->travel_percent_max),
            'overnight_ok'             => $this->toBool($app->overnight_ok),
            'driver_license'           => $this->toBool($app->driver_license),
            'opening_type_accepts'     => $toArray($app->opening_type_accepts),
            'comp_structure_accepts'   => $toArray($app->comp_structure_accepts),
            'expected_base'            => $this->toNumber($app->expected_base),
            'expected_ote'             => $this->toNumber($app->expected_ote),
            'cold_outreach_ok'         => $this->toBool($app->cold_outreach_ok),
            'work_auth'                => $app->work_auth,
            'start_date'               => $this->toDate($app->start_date),
            'has_noncompete_conflict'  => $this->toBool($app->has_noncompete_conflict),
            'background_check_ok'      => $this->toBool($app->background_check_ok),
        ];
    }

    /**
     * Evaluate rules against answers, return [verdict, failCount, flagCount]
     */
    protected function evaluate(array $rules, string $policy, array $answers): array
    {
        if ($policy === 'off' || empty($rules)) {
            return ['pass', 0, 0];
        }

        $fails = 0;
        $flags = 0;

        foreach ($rules as $r) {
            $field = $r['field'] ?? null;
            $op    = $r['op'] ?? null;
            if (!$field || !$op) continue;

            $cand = $answers[$field] ?? null;
            $ok   = $this->compare($cand, $op, $r);
            $sev  = $r['severity'] ?? 'fail';

            if (!$ok) {
                ($sev === 'fail') ? $fails++ : $flags++;
            }
        }

        $verdict = ($fails > 0 && $policy === 'hard') ? 'hard_block'
                 : (($fails > 0 && $policy === 'soft') ? 'soft_block' : 'pass');

        return [$verdict, $fails, $flags];
    }

    /* ---------- low-level comparators & casters ---------- */

    protected function toNumber($v): ?float
    {
        if ($v === '' || is_null($v)) return null;
        return (float) $v;
    }

    protected function toBool($v): ?bool
    {
        if (is_null($v) || $v === '') return null;
        if (is_bool($v)) return $v;
        $map = ['true'=>true,'1'=>true,1=>true,'false'=>false,'0'=>false,0=>false];
        return $map[$v] ?? null;
    }

    protected function toDate($v): ?string
    {
        if (!$v) return null;
        try { return Carbon::parse((string)$v)->toDateString(); }
        catch (\Throwable $e) { return null; }
    }

    /**
     * Supports ops: >=, <=, eq, between, in, contains_any, contains_all
     */
    protected function compare($candidate, string $op, array $rule): bool
    {
        $numeric = fn($x) => ($x === '' || $x === null) ? 0.0 : (float) $x;

        return match ($op) {
            '>=' => $numeric($candidate) >= $numeric($rule['value'] ?? $rule['min'] ?? null),
            '<=' => $numeric($candidate) <= $numeric($rule['value'] ?? $rule['max'] ?? null),
            'eq' => $this->eqCompare($candidate, $rule['value'] ?? null),
            'between' => (function() use ($candidate,$rule,$numeric){
                $min = $numeric($rule['min'] ?? null);
                $max = $numeric($rule['max'] ?? null);
                $val = $numeric($candidate);
                if (!is_null($min) && $val < $min) return false;
                if (!is_null($max) && $val > $max) return false;
                return true;
            })(),
            'in' => in_array($candidate, array_values((array)($rule['value'] ?? [])), true),
            'contains_any' => collect((array)$candidate)->intersect((array)($rule['value'] ?? []))->isNotEmpty(),
            'contains_all' => collect((array)($rule['value'] ?? []))->every(fn($v) => collect((array)$candidate)->contains($v)),
            default => true,
        };
    }

    protected function eqCompare($candidate, $ruleValue): bool
    {
        // Dates
        $rd = $this->toDate($ruleValue);
        $cd = $this->toDate($candidate);
        if ($rd || $cd) return $cd === $rd;

        // Booleans
        $rb = $this->toBool($ruleValue);
        $cb = $this->toBool($candidate);
        if (!is_null($rb) || !is_null($cb)) {
            return $cb === $rb;
        }

        // Fallback string compare
        return (string) $candidate === (string) $ruleValue;
    }
}
