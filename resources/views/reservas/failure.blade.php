@extends('layouts.app')
@section('content')
<div class="container card">
  <h2>Pago no confirmado / Pendiente</h2>
  <p>Reserva #{{ $reserva->id }} quedó en estado: {{ $reserva->estado }}</p>
  <p>Puedes intentar pagar de nuevo desde tu historial.</p>
</div>
@endsection
