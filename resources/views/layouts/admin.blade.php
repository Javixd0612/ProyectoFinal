<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Admin</title>
    <link rel="stylesheet" href="{{ asset('css/admin.css') }}">
</head>
<body>
    <!-- NAVBAR Gamer -->
    <nav class="gamer-nav">
        <div class="container nav-inner">
            <ul class="gamer-menu">
                <li><a href="{{ route('admin.dashboard') }}" class="gamer-link">Dashboard</a></li>
                <li><a href="{{ route('admin.reservas') }}" class="gamer-link">Reservas</a></li>
                <li><a href="{{ route('admin.usuarios') }}" class="gamer-link">Usuarios</a></li>
                <li><a href="{{ route('admin.configuraciones') }}" class="gamer-link">Configuraciones</a></li>
            </ul>
            <div style="margin-left:auto;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="gamer-btn-rect">Salir</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="page-bg page-wrapper">
        @yield('content')
    </main>
</body>
</html>
