{{-- resources/views/reserva/edit.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container page-wrapper">
    <div class="card">
        <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
            <div>
                <h1 class="card-title" style="color:var(--neon-cyan);margin:0 0 6px;">Editar reserva</h1>
                <div class="small" style="color:#bfbfbf;">Actualiza la consola, fecha, hora o duración. Solo reservas en estado <strong>pending</strong>.</div>
            </div>
            <div class="header-actions">
                <a href="{{ route('reserva.index') }}" class="btn-ghost">Volver</a>
            </div>
        </div>

        @if($errors->any())
            <div style="color:#ffb3b3;margin-top:10px;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('reserva.update', $reserva) }}" method="POST" class="mt-4" id="editReservaForm">
            @csrf
            @method('PUT')

            <style>
                /* evitar movimiento al editar descripciones */
                textarea { min-height:64px; max-height:120px; resize:vertical; overflow:auto; }
                .consola-desc { min-height:64px; }
            </style>

            <div style="display:grid; grid-template-columns: 1fr 320px; gap:16px; align-items:start;">
                <div>
                    <label class="small" for="consola_select">Consola</label>
                    <div style="margin-top:8px;">
                        <select id="consola_select" name="consola_id" required class="w-full p-2 border">
                            @foreach($consolas as $c)
                                <option value="{{ $c->id }}" data-precio="{{ $c->precio_hora }}" data-desc="{{ htmlentities($c->descripcion ?? '', ENT_QUOTES, 'UTF-8') }}" @selected($reserva->consola_id == $c->id)>
                                    {{ $c->nombre }} — ${{ number_format($c->precio_hora,0,',','.') }} / h
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="small" style="margin-top:8px;color:#bfbfbf;">
                        <strong>Descripción:</strong>
                        <div id="consola_desc" class="consola-desc" style="margin-top:6px;">
                            {{ optional($consolas->firstWhere('id', $reserva->consola_id))->descripcion ?? '—' }}
                        </div>
                    </div>
                </div>

                <div style="border-left:1px solid rgba(255,255,255,0.02); padding-left:16px;">
                    <div class="small">Precio por hora</div>
                    <div id="precioVista" style="font-weight:700;margin-top:6px;">
                        ${{ number_format(optional($consolas->firstWhere('id', $reserva->consola_id))->precio_hora ?? 0,0,',','.') }}
                    </div>

                    <div style="margin-top:12px;">
                        <label class="small" for="fecha">Fecha</label>
                        <input id="fecha" type="date" name="fecha" required class="w-full p-2 border" value="{{ old('fecha') ?? $reserva->start_at->toDateString() }}" min="{{ \Carbon\Carbon::now()->toDateString() }}">
                    </div>

                    <div style="margin-top:12px;">
                        <label class="small" for="hora_ui">Hora</label>

                        <div style="display:flex;gap:6px;margin-top:6px;">
                            <select id="hora_hour" class="p-2 border" aria-label="Hora (hora)">
                                @for($h=1;$h<=12;$h++)
                                    <option value="{{ $h }}">{{ $h }}</option>
                                @endfor
                            </select>

                            <select id="hora_min" class="p-2 border" aria-label="Minutos">
                                @foreach(['00','15','30','45'] as $m)
                                    <option value="{{ $m }}">{{ $m }}</option>
                                @endforeach
                            </select>

                            <select id="hora_ampm" class="p-2 border" aria-label="AM/PM">
                                <option value="AM">AM</option>
                                <option value="PM">PM</option>
                            </select>
                        </div>

                        <input type="hidden" name="hora" id="hora_hidden" value="{{ old('hora') ?? $reserva->start_at->format('H:i') }}">
                    </div>

                    <div style="margin-top:12px;">
                        <label class="small" for="horas">Horas</label>
                        <select id="horas" name="horas" required class="w-full p-2 border">
                            <option value="1" @selected($reserva->horas==1)>1</option>
                            <option value="2" @selected($reserva->horas==2)>2</option>
                            <option value="3" @selected($reserva->horas==3)>3</option>
                        </select>
                    </div>

                    <div style="margin-top:12px;">
                        <div class="small">Precio estimado</div>
                        <div id="precioEstimado" style="font-weight:700;margin-top:6px;">
                            ${{ number_format($reserva->precio_total ?? (optional($consolas->firstWhere('id', $reserva->consola_id))->precio_hora * $reserva->horas ?? 0), 0, ',', '.') }}
                        </div>
                        <small class="small" style="color:#bfbfbf;">Este valor es una estimación; el servidor calculará el total final.</small>
                    </div>
                </div>
            </div>

            <div style="margin-top:16px; display:flex; gap:8px;">
                <button type="submit" class="gamer-btn-rect">Guardar cambios</button>
                <a href="{{ route('reserva.index') }}" class="btn-ghost" style="align-self:center;">Cancelar</a>
            </div>
        </form>
    </div>
</div>

{{-- SCRIPT INLINE para que siempre funcione --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const consolaSelect = document.getElementById('consola_select');
    const precioVista = document.getElementById('precioVista');
    const precioEstimado = document.getElementById('precioEstimado');
    const descDiv = document.getElementById('consola_desc');
    const horasSelect = document.getElementById('horas');
    const fechaEl = document.getElementById('fecha');

    const hourSel = document.getElementById('hora_hour');
    const minSel = document.getElementById('hora_min');
    const ampmSel = document.getElementById('hora_ampm');
    const horaHidden = document.getElementById('hora_hidden');

    function formatNumber(n) { return Number(n).toLocaleString('es-CO'); }

    function actualizarVista() {
        const opt = consolaSelect.options[consolaSelect.selectedIndex];
        if (!opt) return;
        const precio = parseFloat(opt.dataset.precio) || 0;
        const desc = opt.dataset.desc || '—';
        const horas = parseInt(horasSelect.value || '1', 10);
        precioVista.textContent = '$' + formatNumber(precio);
        precioEstimado.textContent = '$' + formatNumber(precio * horas);
        descDiv.textContent = desc || '—';
    }

    // Evitar fechas pasadas (UI) y ajustar hora minima si hoy
    function ajustarMinHora() {
        const fecha = fechaEl.value;
        if (!fecha) return;
        const selectedDate = new Date(fecha + 'T00:00:00');
        const today = new Date(); const todayDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        if (selectedDate.getTime() === todayDate.getTime()) {
            // no cambiamos selects drásticamente: dejamos lógica ligera
        }
    }

    // sincroniza selects AM/PM al hidden (formato 24h HH:MM)
    function syncHoraHidden() {
        let h = parseInt(hourSel.value || '12', 10);
        const m = (minSel.value || '00');
        const ampm = (ampmSel.value || 'AM');
        if (ampm === 'AM') {
            if (h === 12) h = 0;
        } else {
            if (h !== 12) h = h + 12;
        }
        const hh = String(h).padStart(2,'0');
        horaHidden.value = hh + ':' + String(m).padStart(2,'0');
    }

    // init: si hay hora en horaHidden, parsear y llenar selects
    (function initHoraFromHidden() {
        const initial = horaHidden.value;
        try {
            const parts = initial.split(':');
            if (parts.length >= 2) {
                let hh = parseInt(parts[0],10);
                const mm = parts[1];
                let ampm = 'AM';
                if (hh === 0) { hh = 12; ampm = 'AM'; }
                else if (hh === 12) { ampm = 'PM'; }
                else if (hh > 12) { hh = hh - 12; ampm = 'PM'; }
                hourSel.value = hh;
                minSel.value = mm;
                ampmSel.value = ampm;
            }
        } catch(e){}
        syncHoraHidden();
    })();

    consolaSelect.addEventListener('change', actualizarVista);
    horasSelect.addEventListener('change', actualizarVista);
    fechaEl.addEventListener('change', ajustarMinHora);

    [hourSel, minSel, ampmSel].forEach(el => {
        if (el) el.addEventListener('change', syncHoraHidden);
    });

    // init
    actualizarVista();
    ajustarMinHora();
});
</script>

@endsection
