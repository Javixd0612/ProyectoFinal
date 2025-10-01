<x-app-layout>
    <link rel="stylesheet" href="{{ asset('css/contacto.css') }}">
    <!-- Video de fondo -->
    <video autoplay muted loop class="video-fondo">
        <source src="{{ asset('videos/fondo.mp4') }}" type="video/mp4">
        Tu navegador no soporta video HTML5.
    </video>
    <div class="overlay"></div>

    <div class="container-contacto">
        <h1 class="titulo">Contáctanos</h1>

        <p class="descripcion">
            ¿Tienes dudas o quieres más información sobre <strong>Tecno Juegos</strong>?  
            Escríbenos y te responderemos lo más pronto posible.
        </p>

        {{-- Mensaje de éxito --}}
        @if(session('success'))
            <div class="alerta-exito">
                {{ session('success') }}
            </div>
        @endif

        {{-- Formulario --}}
        <form action="{{ route('contacto.enviar') }}" method="POST" class="formulario">
            @csrf
            <div>
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre" required>
            </div>

            <div>
                <label for="email">Correo</label>
                <input type="email" id="email" name="email" required>
            </div>

            <div>
                <label for="mensaje">Mensaje</label>
                <textarea id="mensaje" name="mensaje" rows="4" required></textarea>
            </div>

            <button type="submit" class="btn-enviar">Enviar Mensaje</button>
        </form>

        <!-- Redes sociales -->
        <div class="redes">
            <a href="https://facebook.com" target="_blank" class="btn-redes fb">Facebook</a>
            <a href="https://instagram.com" target="_blank" class="btn-redes ig">Instagram</a>
            <a href="https://twitter.com" target="_blank" class="btn-redes tw">X (Twitter)</a>
        </div>
    </div>

    <!-- SECCIÓN CÓMO LLEGAR -->
    <div class="seccion-ubicacion">
        <h2 class="subtitulo">📍 Cómo llegar</h2>
        <p>Cra. 74, Tejelo, Medellín, Doce de Octubre, Medellín, Antioquia</p>

        <!-- Mapa Google -->
        <div class="mapa">
            <iframe 
                src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3976.841068084493!2d-75.588!3d6.279!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x8e4429e6c6f9f3b9%3A0x9e7b6c2b1a6b7f92!2sCra.%2074%2C%20Tejelo%2C%20Medell%C3%ADn%2C%20Doce%20de%20Octubre%2C%20Antioquia!5e0!3m2!1ses!2sco!4v1690000000000!5m2!1ses!2sco" 
                width="100%" 
                height="350" 
                style="border:0;" 
                allowfullscreen="" 
                loading="lazy" 
                referrerpolicy="no-referrer-when-downgrade">
            </iframe>
        </div>
    </div>


</x-app-layout>


