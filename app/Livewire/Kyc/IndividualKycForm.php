<?php

namespace App\Livewire\Kyc;

use App\Models\KycSubmission;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class IndividualKycForm extends Component
{
    public ?KycSubmission $submission = null;

    public ?string $full_name = null;
    public ?string $country = null;
    public ?string $region = null;
    public ?string $city = null;
    public ?string $phone = null;
    public ?string $notes = null;

    public function mount(): void
    {
        $user = Auth::user();
        abort_unless($user && $user->isIndividual(), 403, 'This KYC is for individual accounts.');

        // Load latest non-approved submission or create draft
        $this->submission = KycSubmission::where('user_id', $user->id)
            ->orderByDesc('id')
            ->first();

        if (! $this->submission || $this->submission->status === 'approved') {
            $this->submission = KycSubmission::create([
                'user_id' => $user->id,
                'status'  => 'draft',
            ]);
        }

        $this->full_name = $this->submission->full_name;
        $this->country   = $this->submission->country;
        $this->region    = $this->submission->region;
        $this->city      = $this->submission->city;
        $this->phone     = $this->submission->phone;
        $this->notes     = $this->submission->notes;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required','string','max:255'],
            'country'   => ['required','string','max:100'],
            'region'    => ['nullable','string','max:100'],
            'city'      => ['nullable','string','max:100'],
            'phone'     => ['nullable','string','max:40'],
            'notes'     => ['nullable','string','max:2000'],
        ];
    }

    public function saveDraft(): void
    {
        $this->validate([
            'full_name' => ['nullable','string','max:255'],
            'country'   => ['nullable','string','max:100'],
            'region'    => ['nullable','string','max:100'],
            'city'      => ['nullable','string','max:100'],
            'phone'     => ['nullable','string','max:40'],
            'notes'     => ['nullable','string','max:2000'],
        ]);

        $this->submission->fill([
            'full_name' => $this->full_name,
            'country'   => $this->country,
            'region'    => $this->region,
            'city'      => $this->city,
            'phone'     => $this->phone,
            'notes'     => $this->notes,
            'status'    => 'draft',
        ])->save();

        $this->dispatch('toast', body: 'Draft saved.');
    }

    public function submit()
    {
        $this->validate();

        $this->submission->fill([
            'full_name'   => $this->full_name,
            'country'     => $this->country,
            'region'      => $this->region,
            'city'        => $this->city,
            'phone'       => $this->phone,
            'notes'       => $this->notes,
            'status'      => 'pending_review',
            'submitted_at'=> now(),
        ])->save();

        return redirect()->to('/kyc/individual')->with('status', 'submitted');
    }

    public function render()
    {
        return view('livewire.kyc.individual-kyc-form', [
            'currentStatus' => $this->submission->status,
        ]);
    }
}
