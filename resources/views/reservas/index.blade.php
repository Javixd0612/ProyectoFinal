<!-- resources/views/reservas/index.blade.php -->
<x-app-layout>
    <main class="page-bg">
        <div class="py-12 page-wrapper">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 content-area">
                <div class="card">
                    <div class="p-6">
                        <h2>Todas las Reservas</h2>

                        @if($reservas->isEmpty())
                          <p>No hay reservas registradas.</p>
                        @else
                          <table class="table w-full">
                            <thead>
                              <tr><th>ID</th><th>User</th><th>Consola</th><th>Fecha</th><th>Hora</th><th>Precio</th><th>Estado</th></tr>
                            </thead>
                            <tbody>
                              @foreach($reservas as $r)
                                <tr>
                                  <td>{{ $r->id }}</td>
                                  <td>{{ $r->user->name ?? $r->user_id }}</td>
                                  <td>{{ $r->consola->nombre ?? '—' }}</td>
                                  <td>{{ $r->fecha }}</td>
                                  <td>{{ $r->hora_inicio }} - {{ $r->hora_fin }}</td>
                                  <td>${{ number_format($r->precio,0,',','.') }}</td>
                                  <td>{{ ucfirst($r->estado) }}</td>
                                </tr>
                              @endforeach
                            </tbody>
                          </table>
                        @endif

                    </div>
                </div>
            </div>
        </div>
    </main>
</x-app-layout>
