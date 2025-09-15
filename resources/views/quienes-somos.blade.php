<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/quienes-somos.css') }}">

    <!-- Video de fondo -->
    <video autoplay muted loop playsinline class="video-fondo">
        <source src="{{ asset('videos/fondo.mp4') }}" type="video/mp4">
        Tu navegador no soporta video HTML5.
    </video>

    <!-- Overlay con degradado futurista -->
    <div class="overlay"></div>

    <div class="contenedor">
        <!-- Sección Quiénes Somos -->
        <section class="card fade-in">
            <h1>¿QUIÉNES SOMOS?</h1>
            <p>
                En <strong>Tecno Juegos</strong> desarrollamos soluciones digitales para la administración 
                de consolas y reservas en salas de videojuegos. Nuestro objetivo es eliminar la desorganización 
                de procesos manuales y ofrecer una experiencia fluida tanto a jugadores como a administradores.
            </p>
        </section>

        <!-- Misión & Visión -->
        <div class="grid-2">
            <div class="card slide-up">
                <h2>Misión</h2>
                <p>
                    Simplificar la gestión gamer con una plataforma intuitiva, segura y accesible, 
                    potenciando la experiencia de entretenimiento digital.
                </p>
            </div>
            <div class="card slide-up">
                <h2>Visión</h2>
                <p>
                    Ser referentes en Latinoamérica en el sector gamer, 
                    innovando en la forma de reservar, administrar y disfrutar experiencias de videojuegos.
                </p>
            </div>
        </div>

        <!-- Valores -->
        <section class="card fade-in">
            <h2>Valores</h2>
            <div class="tags">
                <span>Innovación</span>
                <span>Confianza</span>
                <span>Pasión por la tecnología</span>
                <span>Eficiencia</span>
            </div>
        </section>

        <!-- Beneficios -->
        <section class="card slide-up">
            <h2>Beneficios</h2>
            <div class="beneficios">
                <div class="beneficio">Reservas rápidas y seguras</div>
                <div class="beneficio">Disponibilidad en tiempo real</div>
                <div class="beneficio">Gestión simplificada</div>
                <div class="beneficio">Optimización de la experiencia gamer</div>
            </div>
        </section>

        <!-- CTA y redes -->
        <div class="cta fade-in">
            <a href="/reserva" class="btn-cta">Reserva tu consola ahora</a>
        </div>
        <div class="redes fade-in">
            <a href="https://facebook.com" target="_blank" class="btn-redes">Facebook</a>
            <a href="https://instagram.com" target="_blank" class="btn-redes">Instagram</a>
            <a href="https://twitter.com" target="_blank" class="btn-redes">X (Twitter)</a>
        </div>
    </div>
</x-app-layout>
