@extends('layouts.app')

@section('content')
<div class="container p-6">
    <h1 class="text-2xl font-bold mb-4">Reservar Consola</h1>

    @if($errors->any())
        <div class="mb-3 text-red-400">
            <ul>
                @foreach($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('success'))
        <div class="mb-3 text-green-400">{{ session('success') }}</div>
    @endif

    <form action="{{ route('reserva.store') }}" method="POST" class="mb-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label>Consola</label>
                <select name="consola_id" required class="w-full p-2 border">
                    <option value="">-- elegir --</option>
                    @foreach($consolas as $c)
                        <option value="{{ $c->id }}" @selected(old('consola_id') == $c->id)>{{ $c->nombre }} — ${{ number_format($c->precio_hora, 0, ',', '.') }} / hora</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Fecha</label>
                <input type="date" name="fecha" class="w-full p-2 border" value="{{ old('fecha') ?? \Carbon\Carbon::now()->toDateString() }}" required>
            </div>

            <div>
                <label>Hora (ej. 14:00)</label>
                <input type="time" name="hora" class="w-full p-2 border" value="{{ old('hora') ?? '12:00' }}" required>
            </div>

            <div>
                <label>Horas</label>
                <select name="horas" class="w-full p-2 border" required>
                    <option value="1" @selected(old('horas')==1)>1</option>
                    <option value="2" @selected(old('horas')==2)>2</option>
                    <option value="3" @selected(old('horas')==3)>3</option>
                </select>
            </div>

            <div class="col-span-3">
                <button class="gamer-btn-rect mt-2">Reservar (queda pendiente)</button>
            </div>
        </div>
    </form>

    <hr class="my-6">

    <h2 class="text-xl font-semibold mb-3">Mis reservas</h2>
    <table class="w-full">
        <thead>
            <tr>
                <th>Consola</th>
                <th>Inicio</th>
                <th>Fin</th>
                <th>Horas</th>
                <th>Precio</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach($misReservas as $r)
                <tr>
                    <td>{{ $r->consola->nombre }}</td>
                    <td>{{ $r->start_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $r->end_at->format('Y-m-d H:i') }}</td>
                    <td>{{ $r->horas }}</td>
                    <td>{{ number_format($r->precio_total, 0, ',', '.') }}</td>
                    <td>{{ ucfirst($r->status) }}</td>
                    <td>
                        @if($r->status === 'pending')
                            <a href="{{ route('reserva.edit', $r) }}" class="btn-ghost mr-1">Editar</a>

                            <form action="{{ route('reserva.destroy', $r) }}" method="POST" style="display:inline">
                                @csrf @method('DELETE')
                                <button class="btn-ghost mr-1" type="submit">Cancelar</button>
                            </form>

                            <form action="{{ route('reserva.pay', $r) }}" method="POST" style="display:inline">
                                @csrf
                                <button class="neon-btn" type="submit">Pagar (sandbox)</button>
                            </form>
                        @else
                            -
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
