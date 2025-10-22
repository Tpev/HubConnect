<x-guest-layout>
    <x-authentication-card>
        <x-slot name="logo">
            <x-authentication-card-logo />
        </x-slot>

        <x-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}" x-data="{ type: '{{ old('account_type','company') }}' }">
            @csrf

            <!-- Account Type -->
            <div class="mt-2">
                <x-label for="account_type" value="Account Type" />
                <div class="mt-1 flex gap-4">
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="account_type" value="company"
                               x-model="type"
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Company</span>
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="radio" name="account_type" value="individual"
                               x-model="type"
                               class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span>Individual</span>
                    </label>
                </div>
                @error('account_type') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <!-- Company Name -->
            <div class="mt-4" x-show="type === 'company'" x-cloak>
                <x-label for="company_name" value="Company Name" />
                <x-input
                    id="company_name"
                    class="block mt-1 w-full"
                    type="text"
                    name="company_name"
                    :value="old('company_name')"
                    x-bind:required="type==='company'"
                    x-bind:disabled="type==='individual'"
                />
                @error('company_name') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <!-- Company Type -->
            <div class="mt-4" x-show="type === 'company'" x-cloak>
                <x-label for="company_type" value="Company Type" />
                <select
                    id="company_type"
                    name="company_type"
                    class="block mt-1 w-full border-gray-300 rounded-md shadow-sm"
                    x-bind:required="type==='company'"
                    x-bind:disabled="type==='individual'"
                >
                    <option value="" disabled {{ old('company_type') ? '' : 'selected' }}>Select one</option>
                    <option value="manufacturer" @selected(old('company_type')==='manufacturer')>Manufacturer</option>
                    <option value="distributor"  @selected(old('company_type')==='distributor')>Distributor</option>
                    <option value="both"         @selected(old('company_type')==='both')>Both</option>
                </select>
                @error('company_type') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
            </div>

            <!-- User Core Fields -->
            <div class="mt-4">
                <x-label for="name" value="{{ __('Name') }}" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name"
                         :value="old('name')" required autofocus autocomplete="name" />
            </div>

            <div class="mt-4">
                <x-label for="email" value="{{ __('Email') }}" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email"
                         :value="old('email')" required autocomplete="username" />
            </div>

            <div class="mt-4">
                <x-label for="password" value="{{ __('Password') }}" />
                <x-input id="password" class="block mt-1 w-full" type="password" name="password"
                         required autocomplete="new-password" />
            </div>

            <div class="mt-4">
                <x-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-input id="password_confirmation" class="block mt-1 w-full" type="password"
                         name="password_confirmation" required autocomplete="new-password" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-label for="terms">
                        <div class="flex items-center">
                            <x-checkbox name="terms" id="terms" required />
                            <div class="ms-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                    'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Terms of Service').'</a>',
                                    'privacy_policy'   => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-6">
                <a class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>
                <x-button class="ms-4">
                    {{ __('Register') }}
                </x-button>
            </div>
        </form>
    </x-authentication-card>
</x-guest-layout>
