<section>
    <header>
        <h2 class="profile-section-title">
            {{ __('Información del Perfil') }}
        </h2>

        <p class="profile-section-text">
            {{ __("Actualiza la información de tu cuenta y tu dirección de correo electrónico.") }}
        </p>
    </header>

    <form id="send-verification" method="post" action="{{ route('verification.send') }}">
        @csrf
    </form>

    <form method="post" action="{{ route('profile.update') }}" class="profile-form">
        @csrf
        @method('patch')

        <div class="form-group">
            <label for="name">{{ __('Nombre') }}</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}" required autofocus autocomplete="name">
            <x-input-error class="alert-error" :messages="$errors->get('name')" />
        </div>

        <div class="form-group">
            <label for="email">{{ __('Email') }}</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="username">
            <x-input-error class="alert-error" :messages="$errors->get('email')" />

            @if ($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <div class="verification-box">
                    <p>{{ __('Tu email no está verificado.') }}</p>
                    <button form="send-verification" class="neon-btn update-btn">
                        {{ __('Reenviar verificación') }}
                    </button>

                    @if (session('status') === 'verification-link-sent')
                        <p class="success-msg">
                            {{ __('Se ha enviado un nuevo enlace de verificación a tu correo.') }}
                        </p>
                    @endif
                </div>
            @endif
        </div>

        <div class="form-actions">
            <button class="neon-btn save-btn">💾 {{ __('Guardar') }}</button>

            @if (session('status') === 'profile-updated')
                <p class="success-msg">{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</section>
