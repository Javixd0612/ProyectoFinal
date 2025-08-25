<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Restablecer Contraseña</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap');
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Orbitron',sans-serif;background:radial-gradient(circle at top,#0f0f0f,#000);color:#fff;display:flex;justify-content:center;align-items:center;height:100vh;padding:1rem;}
        .login-container{width:100%;max-width:400px;padding:1rem;}
        .login-box{background:#111;padding:2rem;border-radius:15px;box-shadow:0 0 15px #00ffcc;}
        .login-title{text-align:center;color:#00ffcc;margin-bottom:1.5rem;font-size:1.8rem;text-shadow:0 0 10px #00ffcc,0 0 20px #00ffcc;}
        .alert-error{background:#ff0033;color:#fff;padding:.8rem 1rem;border-radius:10px;margin-top:.5rem;margin-bottom:1rem;text-align:center;box-shadow:0 0 10px #ff0033;font-weight:bold;font-size:.9rem;}
        .form-group{margin-bottom:1.2rem;}
        .form-group label{display:block;margin-bottom:.5rem;color:#00ffcc;font-size:.95rem;text-shadow:0 0 3px #00ffcc;}
        .form-group input{width:100%;padding:.6rem;border:none;border-radius:8px;background:#1e1e1e;color:#fff;font-size:1rem;outline:none;box-shadow:inset 0 0 5px #00ffcc;transition:.3s;}
        .form-group input:focus{background:#222;box-shadow:0 0 5px #00ffcc,0 0 10px #00ffcc;}
        .form-actions{display:flex;flex-direction:column;align-items:center;margin-top:1.5rem;}
        .neon-btn{background:#ff0099;color:#fff;border:none;padding:12px 25px;font-size:1.1rem;border-radius:10px;text-shadow:0 0 10px #ff0099;box-shadow:0 0 15px #ff0099;transition:.3s;cursor:pointer;}
        .neon-btn:hover{background:#fff;color:#ff0099;text-shadow:none;}
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">Restablecer contraseña</h2>

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus>
                    @error('email')<div class="alert-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="password">Nueva contraseña</label>
                    <input id="password" type="password" name="password" required autocomplete="new-password">
                    @error('password')<div class="alert-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar contraseña</label>
                    <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password">
                    @error('password_confirmation')<div class="alert-error">{{ $message }}</div>@enderror
                </div>

                <div class="form-actions">
                    <button type="submit" class="neon-btn">Restablecer</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
