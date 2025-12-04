<x-guest-layout>
    <x-auth-card>
        <x-slot name="logo">
            <a href="/">
                <x-application-logo class="w-20 h-20 fill-current text-gray-500" />
            </a>
        </x-slot>

        <div class="mb-8 text-center">
            <h1 class="text-2xl font-semibold text-slate-800">{{ __('common.login') }}</h1>
            <p class="mt-2 text-sm text-slate-500">{{ __('common.welcome_back_traveloka') }}</p>
        </div>

        <x-auth-session-status class="mb-4 text-center" :status="session('status')" />
        <x-auth-validation-errors class="mb-4" :errors="$errors" />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            @if(!empty($redirectTo))
                <input type="hidden" name="redirect_to" value="{{ $redirectTo }}">
            @endif

            <div>
                <x-label for="email" :value="__('Email')" />
                <x-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autofocus />
            </div>

            <div>
                <x-label for="password" :value="__('Password')" />
                <x-input id="password" class="block mt-1 w-full"
                                type="password"
                                name="password"
                                required autocomplete="current-password" />
            </div>

            <div class="flex items-center justify-between">
                <label for="remember_me" class="inline-flex items-center text-sm text-slate-500">
                    <input id="remember_me" type="checkbox" class="rounded border-slate-300 text-sky-600 shadow-sm focus:ring-sky-200" name="remember">
                    <span class="ml-2">{{ __('Remember me') }}</span>
                </label>
                @if (Route::has('password.request'))
                    <a class="text-sm font-semibold text-sky-600 hover:text-sky-500" href="{{ route('password.request') }}">
                        {{ __('Forgot your password?') }}
                    </a>
                @endif
            </div>

            <div class="pt-4">
                <x-button class="w-full justify-center">
                    {{ __('Log in') }}
                </x-button>

                <p class="mt-4 text-center text-sm text-slate-500">
                    {{ __('common.new_to_traveloka') }}
                    <a class="font-semibold text-sky-600 hover:text-sky-500"
                       href="{{ route('register', !empty($redirectTo) ? ['redirectTo' => $redirectTo] : []) }}">
                        {{ __('common.create_account') }}
                    </a>
                </p>
            </div>
        </form>

        <div class="flex items-center my-6">
            <div class="flex-grow border-t border-slate-200"></div>
            <span class="mx-3 text-xs uppercase tracking-widest text-slate-400">
                {{ __('common.or_continue_with') }}
            </span>
            <div class="flex-grow border-t border-slate-200"></div>
        </div>

        <div class="space-y-3">
            <a href="{{ route('google.login', !empty($redirectTo) ? ['redirectTo' => $redirectTo] : []) }}"
               class="w-full inline-flex items-center justify-center gap-3 border border-slate-200 rounded-xl py-2.5 text-sm font-semibold text-slate-600 hover:bg-slate-50 transition">
                <svg class="w-5 h-5" viewBox="0 0 48 48" aria-hidden="true">
                    <path fill="#EA4335" d="M24 9.5c3.54 0 6 1.54 7.38 2.83l5.4-5.26C33.64 3.3 29.41 1 24 1 14.82 1 6.99 6.56 3.69 14.22l6.44 5.01C12.06 13.83 17.47 9.5 24 9.5z"/>
                    <path fill="#4285F4" d="M46.5 24.5c0-1.6-.15-3.13-.43-4.61H24v8.72h12.6c-.54 2.7-2.16 4.98-4.62 6.52l7.26 5.69C43.73 36.71 46.5 31.11 46.5 24.5z"/>
                    <path fill="#FBBC05" d="M10.13 28.43a14.47 14.47 0 0 1 0-8.84l-6.44-5.01C1.9 17.68 1 20.99 1 24.5s.9 6.82 2.69 9.92l6.44-5.99z"/>
                    <path fill="#34A853" d="M24 47c6.47 0 11.9-2.13 15.86-5.86l-7.26-5.69c-2.01 1.35-4.59 2.13-8.6 2.13-6.53 0-11.94-4.33-13.87-10.27l-6.44 5C6.99 41.44 14.82 47 24 47z"/>
                    <path fill="none" d="M1 1h46.5v46.5H1z"/>
                </svg>
                <span>{{ __('common.login_with_google') }}</span>
            </a>

            <a href="{{ route('facebook.login', !empty($redirectTo) ? ['redirectTo' => $redirectTo] : []) }}"
               class="w-full inline-flex items-center justify-center gap-3 rounded-xl py-2.5 text-sm font-semibold text-white bg-[#1877F2] hover:bg-[#166FE0] transition">
                <span class="inline-flex items-center justify-center w-5 h-5 rounded-full bg-white text-[#1877F2]">
                    <i class="fab fa-facebook-f text-xs"></i>
                </span>
                <span>Login with Facebook</span>
            </a>
        </div>
    </x-auth-card>
</x-guest-layout>