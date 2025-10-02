{{-- resources/views/companies/show.blade.php --}}
<x-app-layout>
    @php
        // Accept either $company or $team from the controller
        $bound = $company ?? $team ?? null;
    @endphp

    <x-slot name="header">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <h2 class="font-semibold text-xl text-gray-800 truncate">
                    {{ $bound?->name ?? 'Company' }}
                </h2>
                @if($bound)
                    <p class="mt-1 text-sm text-gray-500">
                     
                        @if(!empty($bound->company_type)) • Type: {{ ucfirst($bound->company_type) }} @endif
                    </p>
                @endif
            </div>

            <a href="{{ url()->previous() }}"
               class="hidden sm:inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gray-100 hover:bg-gray-200 text-sm">
                <x-ts-icon name="arrow-uturn-left" class="h-4 w-4"/>
                Back
            </a>
        </div>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">
            @if ($bound)
                {{-- IMPORTANT: pass only the ID to avoid Team->Company type mismatch --}}
                @livewire('companies.show', ['companyId' => $bound->id])
            @else
                <div class="bg-white shadow sm:rounded-2xl p-6">
                    <x-ts-error title="Company not found." description="The requested company could not be loaded." />
                    <a href="{{ url()->previous() }}"
                       class="inline-flex mt-4 px-3 py-1.5 bg-gray-100 rounded-lg hover:bg-gray-200">Back</a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
