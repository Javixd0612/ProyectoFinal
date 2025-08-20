@component('mail::message')
# Reserva cancelada

La reserva #{{ $reserva->id }} para la consola **{{ $reserva->consola->nombre }}** del día {{ $reserva->fecha }} {{ $reserva->hora_inicio }} - {{ $reserva->hora_fin }} ha sido cancelada.

Razón: {{ $reserva->cancel_reason ?? 'No informada' }}

Gracias.
@endcomponent
