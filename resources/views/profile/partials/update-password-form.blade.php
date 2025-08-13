<section>
    <header>
        <h2 class="profile-section-title">
            {{ __('Actualizar Contraseña') }}
        </h2>
        <p class="profile-section-text">
            {{ __('Usa una contraseña segura y única para mantener tu cuenta protegida.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="profile-form">
        @csrf
        @method('put')

        <div class="form-group">
            <label for="update_password_current_password">{{ __('Contraseña Actual') }}</label>
            <input id="update_password_current_password" name="current_password" type="password" autocomplete="current-password">
            <x-input-error class="alert-error" :messages="$errors->updatePassword->get('current_password')" />
        </div>

        <div class="form-group">
            <label for="update_password_password">{{ __('Nueva Contraseña') }}</label>
            <input id="update_password_password" name="password" type="password" autocomplete="new-password">
            <x-input-error class="alert-error" :messages="$errors->updatePassword->get('password')" />
        </div>

        <div class="form-group">
            <label for="update_password_password_confirmation">{{ __('Confirmar Contraseña') }}</label>
            <input id="update_password_password_confirmation" name="password_confirmation" type="password" autocomplete="new-password">
            <x-input-error class="alert-error" :messages="$errors->updatePassword->get('password_confirmation')" />
        </div>

        <div class="form-actions">
            <button class="neon-btn update-btn">🔄 {{ __('Actualizar') }}</button>
            @if (session('status') === 'password-updated')
                <p class="success-msg">{{ __('Guardado.') }}</p>
            @endif
        </div>
    </form>
</section>
