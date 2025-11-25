<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-slate-800">{{ __('common.create_traveloka_account') }}</h1>
            <p class="mt-2 text-sm text-slate-500">{{ __('common.just_a_few_steps') }}</p>
        </div>

        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('register') }}" class="space-y-5">
            @csrf

            @if(!empty($redirectTo))
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            @endif

            <div>
                <x-label for="name" :value="__('Name')" />
                <x-input id="name" class="block mt-1 w-full" type="text" name="name" :value="old('name')" required autofocus />
            </div>

            <div>
                <x-label for="email" :value="__('Email')" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required />
            </div>

            <div>
                <x-label for="password" :value="__('Password')" />
                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="new-password" />
            </div>

            <div>
                <x-label for="password_confirmation" :value="__('Confirm Password')" />
                <x-input id="password_confirmation" class="block mt-1 w-full"
                                type="password"
                                name="password_confirmation" required />
            </div>

            <div class="pt-4 space-y-4">
                <x-button class="w-full justify-center">
                    {{ __('Register') }}
                </x-button>
                <p class="text-center text-sm text-slate-500">
                    {{ __('common.already_registered') }}
                    <a class="font-semibold text-sky-600 hover:text-sky-500"
                       href="{{ route('login', !empty($redirectTo) ? ['redirectTo' => $redirectTo] : []) }}">
                        {{ __('common.login') }}
                    </a>
                </p>
            </div>
        </form>
    </x-auth-card>
</x-guest-layout>