<x-guest-layout>
    <div class="gamer-bg min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        <div>
            <img src="{{ asset('images/gamer-logo.png') }}" alt="Logo" class="w-20 h-20">
        </div>

        <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-gamer-dark shadow-md overflow-hidden sm:rounded-lg">
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email -->
                <div>
                    <x-input-label for="email" :value="__('Email')" class="gamer-label" />
                    <x-text-input id="email" class="gamer-input mt-1 block w-full" type="email" name="email" :value="old('email')" required autofocus />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                <!-- Password -->
                <div class="mt-4">
                    <x-input-label for="password" :value="__('Password')" class="gamer-label" />
                    <x-text-input id="password" class="gamer-input mt-1 block w-full" type="password" name="password" required autocomplete="current-password" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                <!-- Remember Me -->
                <div class="block mt-4">
                    <label for="remember_me" class="inline-flex items-center">
                        <input id="remember_me" type="checkbox" class="gamer-checkbox" name="remember">
                        <span class="ml-2 gamer-label text-sm">{{ __('Remember me') }}</span>
                    </label>
                </div>

                <div class="flex items-center justify-end mt-4">
                    @if (Route::has('password.request'))
                        <a class="underline gamer-link text-sm" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif

                    <x-primary-button class="ml-4 gamer-button">
                        {{ __('Log in') }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-guest-layout>
