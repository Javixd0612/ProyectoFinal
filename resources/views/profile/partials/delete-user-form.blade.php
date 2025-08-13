<section>
    <header>
        <h2 class="profile-section-title danger">
            {{ __('Eliminar Cuenta') }}
        </h2>
        <p class="profile-section-text">
            {{ __('Una vez eliminada tu cuenta, todos tus datos serán borrados permanentemente.') }}
        </p>
    </header>

    <button class="neon-btn danger-btn"
        x-data=""
        x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">
        🗑 {{ __('Eliminar Cuenta') }}
    </button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="post" action="{{ route('profile.destroy') }}" class="profile-form">
            @csrf
            @method('delete')

            <h2 class="profile-section-title danger">
                {{ __('¿Estás seguro que quieres eliminar tu cuenta?') }}
            </h2>
            <p class="profile-section-text">
                {{ __('Introduce tu contraseña para confirmar la eliminación permanente.') }}
            </p>

            <div class="form-group">
                <label for="password">{{ __('Contraseña') }}</label>
                <input id="password" name="password" type="password" placeholder="{{ __('Contraseña') }}">
                <x-input-error class="alert-error" :messages="$errors->userDeletion->get('password')" />
            </div>

            <div class="form-actions">
                <button type="button" class="neon-btn cancel-btn" x-on:click="$dispatch('close')">
                    {{ __('Cancelar') }}
                </button>
                <button class="neon-btn danger-btn">
                    🗑 {{ __('Eliminar Cuenta') }}
                </button>
            </div>
        </form>
    </x-modal>
</section>
