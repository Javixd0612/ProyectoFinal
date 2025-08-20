@extends('layouts.app')
@section('title','Simular Pago')
@section('content')
<div class="container" style="max-width:700px;margin:20px auto;">
  <h2>Simulación de pago</h2>
  <p>Reserva #{{ $reserva->id }} — {{ $reserva->consola->nombre ?? '' }}</p>
  <p>Fecha: {{ $reserva->fecha }} — {{ $reserva->hora_inicio }} a {{ $reserva->hora_fin }}</p>
  <p>Precio: ${{ number_format($reserva->precio,0,',','.') }}</p>

  <form method="POST" action="{{ route('reservas.simulate.confirm', $reserva->id) }}">
    @csrf
    <button class="neon-btn" type="submit">Confirmar pago (Simulado)</button>
  </form>
</div>
@endsection
