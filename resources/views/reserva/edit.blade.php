@extends('layouts.app')

@section('content')
<div class="container p-6">
    <h1 class="text-2xl mb-4">Editar reserva</h1>

    <form action="{{ route('reserva.update', $reserva) }}" method="POST">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
            <div>
                <label>Consola</label>
                <select name="consola_id" required class="w-full p-2 border">
                    @foreach($consolas as $c)
                        <option value="{{ $c->id }}" @selected($reserva->consola_id == $c->id)>{{ $c->nombre }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label>Fecha</label>
                <input type="date" name="fecha" class="w-full p-2 border" value="{{ $reserva->start_at->toDateString() }}" required>
            </div>

            <div>
                <label>Hora</label>
                <input type="time" name="hora" class="w-full p-2 border" value="{{ $reserva->start_at->format('H:i') }}" required>
            </div>

            <div>
                <label>Horas</label>
                <select name="horas" class="w-full p-2 border" required>
                    <option value="1" @selected($reserva->horas==1)>1</option>
                    <option value="2" @selected($reserva->horas==2)>2</option>
                    <option value="3" @selected($reserva->horas==3)>3</option>
                </select>
            </div>

            <div class="col-span-3">
                <button class="gamer-btn-rect mt-2">Guardar cambios</button>
            </div>
        </div>
    </form>
</div>
@endsection
