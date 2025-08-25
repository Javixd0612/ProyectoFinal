<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar Correo</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap');
        *{margin:0;padding:0;box-sizing:border-box;}
        body{font-family:'Orbitron',sans-serif;background:radial-gradient(circle at top,#0f0f0f,#000);color:#fff;display:flex;justify-content:center;align-items:center;height:100vh;padding:1rem;}
        .login-container{width:100%;max-width:400px;padding:1rem;}
        .login-box{background:#111;padding:2rem;border-radius:15px;box-shadow:0 0 15px #00ffcc;}
        .login-title{text-align:center;color:#00ffcc;margin-bottom:1.5rem;font-size:1.8rem;text-shadow:0 0 10px #00ffcc,0 0 20px #00ffcc;}
        .alert-success{background:#00cc66;color:#fff;padding:.8rem 1rem;border-radius:10px;margin-top:.5rem;margin-bottom:1rem;text-align:center;box-shadow:0 0 10px #00cc66;font-weight:bold;font-size:.9rem;}
        .form-actions{display:flex;flex-direction:column;align-items:center;margin-top:1.5rem;}
        .neon-btn{background:#ff0099;color:#fff;border:none;padding:12px 25px;font-size:1.1rem;border-radius:10px;text-shadow:0 0 10px #ff0099;box-shadow:0 0 15px #ff0099;transition:.3s;cursor:pointer;}
        .neon-btn:hover{background:#fff;color:#ff0099;text-shadow:none;}
        .forgot-link{color:#aaa;font-size:.9rem;text-decoration:none;transition:color .3s;margin-top:1rem;}
        .forgot-link:hover{text-decoration:underline;color:#00ffcc;}
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-box">
            <h2 class="login-title">Verifica tu correo</h2>

            <p style="text-align:center;margin-bottom:1.5rem;color:#fff;text-shadow:0 0 5px #00ffcc;">
                Gracias por registrarte. Antes de comenzar, revisa tu correo y haz clic en el enlace de verificación.  
                Si no lo recibiste, puedes solicitar otro.
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="alert-success">
                    Se envió un nuevo enlace de verificación a tu correo.
                </div>
            @endif

            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <div class="form-actions">
                    <button type="submit" class="neon-btn">Reenviar correo</button>
                </div>
            </form>

            <form method="POST" action="{{ route('logout') }}" style="margin-top:1rem;text-align:center;">
                @csrf
                <button type="submit" class="forgot-link">Cerrar sesión</button>
            </form>
        </div>
    </div>
</body>
</html>
