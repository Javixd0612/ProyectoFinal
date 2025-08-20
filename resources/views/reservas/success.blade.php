@extends('layouts.app')
@section('content')
<div class="container card">
  <h2>Pago Aprobado</h2>
  <p>Reserva #{{ $reserva->id }} confirmada.</p>
  <p>Consola: {{ $reserva->consola->nombre }}</p>
  <p>Fecha: {{ $reserva->fecha }}</p>
  <p>Hora: {{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }}</p>
  <p>Precio: ${{ $reserva->precio }}</p>
</div>
@endsection
