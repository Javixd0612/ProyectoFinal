{{-- resources/views/admin/reservas.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="page-bg admin-page">
    <div class="container page-wrapper">
        <div class="admin-card dashboard-card p-6 rounded-md shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h1 class="card-title text-2xl font-bold">Panel de Administración</h1>
                    <div class="card-sub">Gestiona las reservas — marca pagadas, elimina o ajusta precios.</div>
                </div>

                <div class="flex items-center gap-3">
                    <a href="{{ route('dashboard') }}" class="gamer-btn-rect">Ir al inicio</a>
                    <a href="{{ route('admin.reservas') }}" class="gamer-btn-rect">Refrescar</a>
                </div>
            </div>

            @if(session('success'))
                <div class="alert-success mb-4">{{ session('success') }}</div>
            @endif

            @if($errors && $errors->any())
                <div class="alert-error mb-4">
                    <ul style="margin:0; padding-left:1rem;">
                        @foreach($errors->all() as $e)
                            <li>{{ $e }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Estadísticas --}}
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 mb-6">
                <div class="card small admin-card p-3">
                    <div class="small">Total ganancias (pagadas)</div>
                    <div style="font-weight:700; font-size:1.1rem; margin-top:6px">${{ number_format($totalGanancias ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="card small admin-card p-3">
                    <div class="small">Total pendientes</div>
                    <div style="font-weight:700; font-size:1.1rem; margin-top:6px">${{ number_format($totalPendientes ?? 0, 0, ',', '.') }}</div>
                </div>
                <div class="card small admin-card p-3">
                    <div class="small">Reservas pagadas</div>
                    <div style="font-weight:700; font-size:1.1rem; margin-top:6px">{{ $pagadas ?? 0 }}</div>
                </div>
                <div class="card small admin-card p-3">
                    <div class="small">Reservas pendientes</div>
                    <div style="font-weight:700; font-size:1.1rem; margin-top:6px">{{ $pendientes ?? 0 }}</div>
                </div>
            </div>

            {{-- Tabla de reservas --}}
            <div class="card small p-3">
                <div class="overflow-x-auto">
                    <table class="table" style="min-width:1000px">
                        <thead>
                            <tr>
                                <th>Usuario</th>
                                <th>Consola</th>
                                <th>Inicio</th>
                                <th>Fin</th>
                                <th>Precio</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($reservas as $r)
                                <tr>
                                    <td style="min-width:200px;">
                                        <strong>{{ $r->user->name ?? '—' }}</strong><br>
                                        <small class="small">{{ $r->user->email ?? '' }}</small>
                                    </td>

                                    <td style="min-width:220px;">
                                        <div>{{ $r->consola->nombre ?? '—' }}</div>

                                        @if(isset($r->consola))
                                        <form action="{{ route('admin.consolas.update_price', $r->consola) }}" method="POST" style="margin-top:6px; display:flex; gap:8px; align-items:center;">
                                            @csrf
                                            <input name="precio_hora" value="{{ $r->consola->precio_hora }}" class="p-1 w-28" />
                                            <button class="btn-ghost" type="submit">Guardar</button>
                                        </form>
                                        @endif
                                    </td>

                                    <td>{{ optional($r->start_at)->format('Y-m-d H:i') }}</td>
                                    <td>{{ optional($r->end_at)->format('Y-m-d H:i') }}</td>
                                    <td>${{ number_format($r->precio_total ?? 0, 0, ',', '.') }}</td>
                                    <td>{{ ucfirst($r->status ?? '—') }}</td>

                                    <td style="min-width:220px;">
                                        @if(($r->status ?? '') !== 'paid')
                                            <form action="{{ route('admin.reservas.mark_paid', $r) }}" method="POST" style="display:inline-block; margin-right:8px;">
                                                @csrf
                                                <button class="neon-btn" type="submit" style="padding:6px 10px; font-size:0.85rem;">Marcar pagada</button>
                                            </form>
                                        @endif

                                        <form action="{{ route('admin.reservas.destroy', $r) }}" method="POST" style="display:inline-block;">
                                            @csrf @method('DELETE')
                                            <button class="btn-ghost" type="submit" onclick="return confirm('¿Eliminar reserva? Esta acción no tiene vuelta atrás.')">Eliminar</button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 text-center">No hay reservas todavía.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="mt-4 small muted">
                Si quieres, puedo añadir búsqueda, paginación y filtros por estado o consola.
            </div>
        </div>
    </div>
</div>
@endsection
