<!-- resources/views/reservas/mis.blade.php -->
<x-app-layout>
    <main class="page-bg">
        <div class="py-12 page-wrapper">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 content-area">
                <div class="card">
                    <div class="p-6">
                        <h2>Mi reserva</h2>

                        @if(session('success')) <div class="alert-success">{{ session('success') }}</div> @endif
                        @if($errors->any()) <div class="alert-error">{{ $errors->first() }}</div> @endif

                        @if(empty($reservaActiva))
                            <p>No tienes una reserva activa. <a href="{{ route('reservas.create') }}">Reservar ahora</a>.</p>
                        @else
                            <div style="margin-bottom:1rem;padding:1rem;border:1px solid rgba(255,255,255,0.03);border-radius:8px;">
                                <strong>Reserva #{{ $reservaActiva->id }}</strong><br>
                                Consola: {{ $reservaActiva->consola->nombre ?? '—' }}<br>
                                Fecha: {{ $reservaActiva->fecha }}<br>
                                Hora: {{ $reservaActiva->hora_inicio }} - {{ $reservaActiva->hora_fin }}<br>
                                Precio: ${{ number_format($reservaActiva->precio,0,',','.') }}<br>
                                Estado: {{ ucfirst($reservaActiva->estado) }}<br>

                                @if($reservaActiva->estado !== 'cancelada' && (Auth::id() == $reservaActiva->user_id || auth()->user()->role == 'admin'))
                                    <form action="{{ route('reservas.cancelar', $reservaActiva->id) }}" method="POST" style="display:inline;margin-top:.6rem;">
                                        @csrf
                                        <button class="btn-ghost" onclick="return confirm('¿Confirmar cancelación?')">Cancelar reserva</button>
                                    </form>
                                @endif
                            </div>
                        @endif

                        {{-- Historial --}}
                        <h3>Historial</h3>
                        @if($historial->isEmpty())
                            <p>No tienes reservas pasadas.</p>
                        @else
                            <ul>
                                @foreach($historial as $h)
                                    <li>#{{ $h->id }} — {{ $h->fecha }} — {{ $h->consola->nombre ?? '—' }} — {{ ucfirst($h->estado) }}</li>
                                @endforeach
                            </ul>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
