<x-app-layout>
    <main class="{{ request()->is('profile') 
            ? 'profile-page' 
            : (Auth::user()->hasRole('admin') ? 'admin-page' : 'page-bg') }}">
            
        <link rel="stylesheet" href="{{ asset('css/inicio.css') }}">

        <div class="inicio-container">

            {{-- Bienvenida --}}
            <section class="bienvenida">
                <h1 class="titulo">🎮 Bienvenido, {{ Auth::user()->name }}!</h1>
                <p class="subtitulo">Tu centro gamer donde la diversión nunca termina.</p>
            </section>

            {{-- Consolas --}}
            <section class="seccion">
                <h2 class="seccion-titulo"> Consolas Disponibles</h2>
                <div class="tarjetas-grid">
                    <div class="tarjeta">
                        <img src="{{ asset('images/prueba.jpg') }}" alt="PS4">
                        <div class="tarjeta-info">
                            <h3>PlayStation 5</h3>
                            <p>Hora 5.000</p>
                        </div>
                    </div>
                    <div class="tarjeta">
                        <img src="{{ asset('images/ps4.png.jpg') }}" alt="Xbox">
                        <div class="tarjeta-info">
                            <h3>PlayStation 4</h3>
                            <p>Hora 4.500</p>
                        </div>
                    </div>
                    <div class="tarjeta">
                        <img src="{{ asset('images/play3.jpg') }}" alt="Switch">
                        <div class="tarjeta-info">
                            <h3>PlayStation 3</h3>
                            <p>Hora 4.000</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Consolas --}}
            <section class="seccion">
                <h2 class="seccion-titulo"> </h2>
                <div class="tarjetas-grid">
                    <div class="tarjeta">
                        <img src="{{ asset('images/xbox.jpg') }}" alt="PS4">
                        <div class="tarjeta-info">
                            <h3>Xbox Series</h3>
                            <p>Hora 3.500</p>
                        </div>
                    </div>
                    <div class="tarjeta">
                        <img src="{{ asset('images/xbox360.jpg') }}" alt="Xbox">
                        <div class="tarjeta-info">
                            <h3>Xbox 360</h3>
                            <p>Hora 3.000</p>
                        </div>
                    </div>
                    <div class="tarjeta">
                        <img src="{{ asset('images/nintendo.jpg') }}" alt="Switch">
                        <div class="tarjeta-info">
                            <h3>Nintendo Switch</h3>
                            <p>Proximamente...</p>
                        </div>
                    </div>
                </div>
            </section>

            {{-- Eventos --}}
            <section class="seccion">
                <h2 class="seccion-titulo"> Últimos Eventos</h2>
                <div class="tarjetas-grid">
                    <div class="tarjeta">
                        <img src="{{ asset('images/eventos/evento1.jpg') }}" alt="Evento 1">
                        <div class="tarjeta-info"><h3>Torneo FIFA</h3></div>
                    </div>
                    <div class="tarjeta">
                        <img src="{{ asset('images/eventos/evento2.jpg') }}" alt="Evento 2">
                        <div class="tarjeta-info"><h3>Torneo Mario Kart</h3></div>
                    </div>
                    <div class="tarjeta">
                        <img src="{{ asset('images/eventos/evento3.jpg') }}" alt="Evento 3">
                        <div class="tarjeta-info"><h3>Torneo Dragon Ball Z Kakarot</h3></div>
                    </div>
                </div>
            </section>

            {{-- Promociones --}}
            <section class="seccion">
                <h2 class="seccion-titulo">Promociones</h2>
                <div class="promos-grid">
                    <div class="promo">
                        <h3>2x1 Martes Gamer</h3>
                        <p>Ven con un amigo y paga solo una hora.</p>
                    </div>
                    <div class="promo">
                        <h3> Happy Hour</h3>
                        <p>De 4 a 6 PM, consolas al 50%.</p>
                    </div>
                    <div class="promo">
                        <h3> Torneo Semanal</h3>
                        <p>Participa y gana horas gratis.</p>
                    </div>
                </div>
            </section>

            {{-- Footer --}}
            <footer class="footer">
                <p>⏰ Lunes a Domingo 10AM - 10PM | 📍 Cra. 74, Tejelo, Medellín, Doce de Octubre, Medellín, Antioquia</p>
                <div class="redes">
                    <a href="#" class="fb"><i class="fab fa-facebook"></i></a>
                    <a href="#" class="ig"><i class="fab fa-instagram"></i></a>
                    <a href="#" class="tt"><i class="fab fa-tiktok"></i></a>
                </div>
            </footer>

        </div>
    </main>
    <script src="https://kit.fontawesome.com/a076d05399.js" crossorigin="anonymous"></script>
</x-app-layout>
