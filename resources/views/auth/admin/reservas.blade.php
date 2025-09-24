@extends('layouts.app')

@section('content')
<div class="container">
    <div class="dashboard-card card">
        <h1 class="text-2xl font-bold mb-4" style="color:var(--neon-cyan)">Panel Admin - Reservas</h1>

        @if(session('success'))
            <div class="alert-success">{{ session('success') }}</div>
        @endif

        <div class="mb-6 small">
            <strong>Total ganancias (pagadas):</strong> ${{ number_format($totalGanancias ?? 0, 0, ',', '.') }} <br>
            <strong>Total pendientes:</strong> ${{ number_format($totalPendientes ?? 0, 0, ',', '.') }} <br>
            <strong>Reservas pagadas:</strong> {{ $pagadas ?? 0 }} — <strong>Pendientes:</strong> {{ $pendientes ?? 0 }}
        </div>

        <div class="card small">
            <table class="table">
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
                            <td>{{ $r->user->name ?? '—' }}<br>
                                <small class="small">{{ $r->user->email ?? '' }}</small>
                            </td>
                            <td>
                                {{ $r->consola->nombre ?? '—' }} <br>
                                @if(isset($r->consola))
                                <form action="{{ route('admin.consolas.update_price', $r->consola) }}" method="POST" class="mt-1">
                                    @csrf
                                    <input name="precio_hora" value="{{ $r->consola->precio_hora }}" class="p-1 w-24 inline-block">
                                    <button class="btn-ghost">Guardar precio</button>
                                </form>
                                @endif
                            </td>
                            <td>{{ optional($r->start_at)->format('Y-m-d H:i') }}</td>
                            <td>{{ optional($r->end_at)->format('Y-m-d H:i') }}</td>
                            <td>${{ number_format($r->precio_total ?? 0, 0, ',', '.') }}</td>
                            <td>{{ ucfirst($r->status ?? '—') }}</td>
                            <td>
                                @if(($r->status ?? '') !== 'paid')
                                    <form action="{{ route('admin.reservas.mark_paid', $r) }}" method="POST" style="display:inline">
                                        @csrf
                                        <button class="neon-btn" style="padding:6px 8px; font-size:0.85rem; margin-bottom:6px">Marcar pagada</button>
                                    </form>
                                @endif

                                <form action="{{ route('admin.reservas.destroy', $r) }}" method="POST" style="display:inline">
                                    @csrf @method('DELETE')
                                    <button class="btn-ghost" onclick="return confirm('Eliminar reserva?')">Eliminar</button>
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
</div>
@endsection
