<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Epic G4mes - Inicio</title>
    <!-- Vincular CSS externo -->
    <link href="{{ asset('css/gamer-style.css') }}" rel="stylesheet">
    <!-- Fuente gamer -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@500;700&display=swap" rel="stylesheet">
</head>
<body>
    <div class="container">
        <header>
            <h1 class="logo">TecnoJuegos</h1>
            <nav>
                <a href="{{ route('login') }}" class="btn">Inicia Sesion</a>
                <a href="{{ route('register') }}" class="btn">Registrate</a>
            </nav>
        </header>

        <main>
            <div class="card">
                <h2>¡Bienvenido a TecnoJuegos!</h2>
                <p>Explora la nueva forma de reservar consolas y jugar como un verdadero gamer.</p>
                <a href="{{ route('register') }}" class="btn neon-btn">Comienza ahora</a>
            </div>
        </main>
    </div>
</body>
</html>
