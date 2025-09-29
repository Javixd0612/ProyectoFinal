<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro | TenoJuegos 🎮</title>
    <link href="{{ asset('css/gamer.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">

    {{-- ✅ Script de Google reCAPTCHA --}}
    {!! NoCaptcha::renderJs() !!}
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">TenoJuegos 🎮</h2>

            {{-- ✅ Mensaje de éxito si se envió el correo de verificación --}}
            @if (session('status'))
                <div class="alert-success">
                    {{ session('status') }}
                </div>
            @endif

            {{-- ⚠️ Alerta si hay errores en el formulario --}}
            @if ($errors->any())
                <div class="alert-error">
                    <strong>⚠️ Error en el registro:</strong> Verifica los campos ingresados.
                </div>
            @endif

            <form method="POST" action="{{ route('register') }}">
                @csrf

                <div class="form-group">
                    <label for="name">Nombre de Jugador</label>
                    <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus>
                    @error('name')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="email">Correo Electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required>
                    @error('email')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Contraseña</label>
                    <input id="password" type="password" name="password" required>
                    @error('password')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar Contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required>
                    @error('password_confirmation')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>

                {{-- ✅ reCAPTCHA aquí --}}
                <div class="form-group recaptcha-container">
                    {!! NoCaptcha::display(['data-callback' => 'enableRegisterButton']) !!}
                    @error('g-recaptcha-response')
                        <div class="alert-error">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-actions">
                    <button type="submit" id="register-btn" class="neon-btn" disabled>
                        Registrarse
                    </button>
                    <a class="forgot-link" href="{{ route('login') }}">
                        ¿Ya tienes cuenta? Inicia sesión
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- ✅ Script que habilita el botón cuando se marca reCAPTCHA --}}
    <script>
        function enableRegisterButton() {
            document.getElementById('register-btn').removeAttribute('disabled');
        }
    </script>

    <script>
document.querySelector("form").addEventListener("submit", function(e) {
    if (grecaptcha.getResponse().length === 0) {
        e.preventDefault();
        alert("⚠️ Necesitas marcar el reCAPTCHA para poder registrarte.");
    }
});
</script>

</body>
</html>
