<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/quienes-somos.css') }}">

    <!-- Video de fondo -->
    <video autoplay muted loop playsinline class="video-fondo">
        <source src="{{ asset('videos/fondo.mp4') }}" type="video/mp4">
        Tu navegador no soporta video HTML5.
    </video>

    <!-- Overlay con efecto futurista -->
    <div class="overlay"></div>

    <div class="contenedor">
        <!-- Título principal -->
        <section class="hero-text fade-in">
            <h1>Bienvenido a <span class="neon">Tecno Juegos</span></h1>
            <p>
                El parche gamer del barrio donde venís a jugar, pasarla bueno y reservar tu consola sin enredos. Tecnología al alcance de todos para que no pierdas tiempo y te dediques a lo que importa: divertirte.
            </p>
        </section>

        <!-- Sección de tarjetas dinámicas -->
        <div class="grid-3">
            <div class="card feature tilt">
                <h2><i class="fa-solid fa-bolt"></i> Velocidad</h2>
                <p>Reservas al toque y confirmación inmediata para que no te quedés esperando.</p>
            </div>
            <div class="card feature tilt">
                <h2><i class="fa-solid fa-shield-halved"></i> Seguridad</h2>
                <p>Pagos protegidos y datos seguros para que jugues sin preocuparte.</p>
            </div>
            <div class="card feature tilt">
                <h2><i class="fa-solid fa-gamepad"></i> Experiencia Gamer</h2>
                <p>Todo está pensado para vos: fácil de usar, rápido y con la mejor vibra.</p>
            </div>
        </div>

        <!-- Misión y visión en formato moderno -->
        <div class="grid-2">
            <div class="card glass slide-up">
                <h2>Misión</h2>
                <p>Llevar la experiencia gamer al barrio, conectando a todos los que aman jugar.</p>
            </div>
            <div class="card glass slide-up">
                <h2>Visión</h2>
                <p>Ser el lugar preferido de los gamers de Medellín y alrededores, el punto de encuentro para jugar y pasar un buen rato.</p>
            </div>
        </div>

        <!-- Sección interactiva "Stats" -->
        <section class="stats-container fade-in">
            <div class="stat">
                <span class="number">+40</span>
                <span class="label">Reservas a la semana</span>
            </div>
            <div class="stat">
                <span class="number">99%</span>
                <span class="label">Satisfacción</span>
            </div>
            <div class="stat">
                <span class="number">Lunes - Domingo</span>
                <span class="label">Disponibilidad</span>
            </div>
        </section>

        <!-- CTA -->
        <div class="cta fade-in">
            <a href="/reserva" class="btn-cta">Reserva Ahora</a>
        </div>

        <!-- Redes sociales -->
        <div class="redes fade-in">
            <a href="https://facebook.com" target="_blank" class="btn-redes">Facebook</a>
            <a href="https://instagram.com" target="_blank" class="btn-redes">Instagram</a>
            <a href="https://twitter.com" target="_blank" class="btn-redes">X (Twitter)</a>
        </div>
    </div>
</x-app-layout>
