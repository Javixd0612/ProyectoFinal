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

        <form action="#" method="POST" class="formulario">
            @csrf
            <div>
                <label for="nombre">Nombre</label>
                <input type="text" id="nombre" name="nombre">
            </div>

            <div>
                <label for="email">Correo</label>
                <input type="email" id="email" name="email">
            </div>

            <div>
                <label for="mensaje">Mensaje</label>
                <textarea id="mensaje" name="mensaje" rows="4"></textarea>
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
</x-app-layout>
