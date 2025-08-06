<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Gamer Portal</title>
    <link href="{{ asset('css/gamer.css') }}" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">TenoJuegos🎮</h2>

            {{-- Mensaje de error si hay problemas con las credenciales --}}
            @if ($errors->any())
                <div class="alert-error">
                    <strong>⚠️ Credenciales incorrectas:</strong> verifica tu email y contraseña.
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <div class="form-group">
                    <label for="email">Email</label>
                    <input id="email" type="email" name="email" required autofocus>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input id="password" type="password" name="password" required>
                </div>

                <div class="form-remember">
                    <label>
                        <input type="checkbox" name="remember"> Recordarme
                    </label>
                </div>

                <div class="form-actions">
                    <button type="submit" class="neon-btn"> Iniciar Sesión</button>
                    @if (Route::has('password.request'))
                        <a class="forgot-link" href="{{ route('password.request') }}">
                            ¿Olvidaste tu contraseña?
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>
</body>
</html>
