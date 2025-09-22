<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background-color: #f4f6f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .header {
            background: linear-gradient(135deg, #0066ff, #00c3ff);
            color: #fff;
            text-align: center;
            padding: 25px;
        }
        .header h1 {
            margin: 0;
            font-size: 22px;
        }
        .content {
            padding: 25px;
            color: #333;
        }
        .content p {
            font-size: 15px;
            margin: 10px 0;
        }
        .content strong {
            color: #0066ff;
        }
        .footer {
            background: #f0f0f0;
            text-align: center;
            padding: 15px;
            font-size: 13px;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Encabezado -->
        <div class="header">
            <h1>📩 Nuevo mensaje de contacto</h1>
        </div>

        <!-- Contenido -->
        <div class="content">
            <p><strong>👤 Nombre:</strong> {{ $nombre }}</p>
            <p><strong>📧 Email:</strong> {{ $email }}</p>
            <p><strong>💬 Mensaje:</strong></p>
            <p>{{ $mensaje }}</p>
        </div>

        <!-- Footer -->
        <div class="footer">
            Este correo fue enviado desde el formulario de <strong>Tecno Juegos</strong>.
        </div>
    </div>
</body>
</html>
