<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirma tu correo</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: 'Orbitron', Arial, sans-serif;
            background: linear-gradient(135deg, #0f0c29, #302b63, #24243e);
            color: #e5e5e5;
        }
        .container {
            width: 100%;
            padding: 40px 0;
            display: flex;
            justify-content: center;
        }
        .card {
            background: rgba(20, 20, 40, 0.9);
            padding: 40px;
            border-radius: 15px;
            box-shadow: 0 0 25px rgba(0, 255, 200, 0.6);
            max-width: 600px;
            text-align: center;
        }
        h1 {
            color: #00ffc6;
            font-size: 26px;
            margin-bottom: 20px;
            text-shadow: 0 0 10px #00ffc6, 0 0 20px #00ffc6;
        }
        p {
            font-size: 16px;
            margin-bottom: 30px;
            color: #cbd5e1;
        }
        .btn {
            display: inline-block;
            padding: 14px 28px;
            background: linear-gradient(90deg, #00ffc6, #007cf0);
            color: #111;
            font-size: 18px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: bold;
            box-shadow: 0 0 15px #00ffc6, 0 0 25px #007cf0;
            transition: transform 0.2s ease-in-out, box-shadow 0.3s;
        }
        .btn:hover {
            transform: scale(1.1);
            box-shadow: 0 0 20px #00ffc6, 0 0 35px #007cf0;
        }
        .footer {
            margin-top: 25px;
            font-size: 13px;
            color: #94a3b8;
        }
        /* Importar fuente gamer */
        @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap');
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1>⚡ ¡Confirma tu correo, Gamer!</h1>
            <p>
                Estás a un paso de unirte a la comunidad.  
                Haz clic en el botón para validar tu cuenta y comenzar la partida 🚀
            </p>
            <a href="{{ url('/verify-pre/'.$token) }}" class="btn">
                Confirmar mi cuenta
            </a>
            <p class="footer">
                Si no fuiste tú quien se registró, ignora este mensaje.  
                GG 👾
            </p>
        </div>
    </div>
</body>
</html>
