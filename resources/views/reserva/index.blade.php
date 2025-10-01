{{-- resources/views/reserva/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container page-wrapper">
    {{-- Mensajes / Errores --}}
    <div class="card" style="margin-bottom:12px;">
        <h1 class="card-title" style="color:var(--neon-cyan);margin:0 0 6px 0;">Reservar Consola</h1>
        <div class="card-sub small" style="margin-bottom:8px;">Selecciona consola, fecha, hora y duración. La reserva quedará en <strong>pending</strong> hasta que la pagues.</div>

        @if($errors->any())
            <div class="mb-3" style="color:#ffb3b3;">
                <ul style="margin:0;padding-left:18px;">
                    @foreach($errors->all() as $e)
                        <li>{{ $e }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if(session('success'))
            <div class="mb-3" style="color:#b5f4c9;">{{ session('success') }}</div>
        @endif

        {{-- estilos locales que ayudan a mantener la nav a la derecha y evitar movimiento de textarea --}}
        <style>
            /* Fuerzo que la zona de perfil en la nav quede a la derecha si existe (no rompe si no existe) */
            .gamer-nav .nav-inner { justify-content: space-between; }
            .gamer-nav .profile-area { margin-left: auto; }

            /* Textareas fijas para que no rompan layout al seleccionar/copiar */
            .consola-grid textarea,
            .consola-grid input[name="descripcion"] {
                min-height:64px;
                max-height:120px;
                resize: vertical;
                overflow: auto;
                box-sizing: border-box;
            }

            /* Aseguro que las tarjetas no cambien tamaño al interactuar con textarea */
            .consola-grid .consola-card { display:flex; flex-direction:column; gap:8px; align-items:stretch; }
        </style>

        {{-- Form reserva --}}
        <form action="{{ route('reserva.store') }}" method="POST" class="mb-0" id="reservaForm">
            @csrf

            {{-- Consolas (tarjetas) --}}
            <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;">
                <div style="flex:1 1 520px;">
                    <label class="small" style="display:block;margin-bottom:6px;">Elige una consola</label>
                    <div class="consola-grid" role="list">
                        @foreach($consolas as $c)
                            <button type="button"
                                role="listitem"
                                tabindex="0"
                                class="card consola-btn"
                                data-id="{{ $c->id }}"
                                data-precio="{{ $c->precio_hora }}"
                                data-desc="{{ htmlspecialchars($c->descripcion ?? '—', ENT_QUOTES, 'UTF-8') }}"
                                style="text-align:left;cursor:pointer;display:flex;flex-direction:column;gap:8px;min-height:120px;"
                                aria-pressed="false"
                                title="Seleccionar {{ $c->nombre }}">
                                <div style="display:flex;justify-content:space-between;align-items:center;">
                                    <div style="font-weight:700;color:var(--neon-cyan);">{{ $c->nombre }}</div>
                                    <div style="font-weight:700;">${{ number_format($c->precio_hora, 0, ',', '.') }}/h</div>
                                </div>
                                <div class="small" style="color:#bfbfbf;">{{ $c->descripcion ?? '—' }}</div>
                                <div style="margin-top:auto;">
                                    <small class="small">Haz clic para seleccionar</small>
                                </div>
                            </button>
                        @endforeach
                    </div>

                    {{-- Fallback select --}}
                    <div style="margin-top:10px;">
                        <label class="small" for="consola_select">O usa la lista</label>
                        <select id="consola_select" class="w-full p-2 border" style="margin-top:6px;">
                            <option value="">-- elegir --</option>
                            @foreach($consolas as $c)
                                <option value="{{ $c->id }}" data-precio="{{ $c->precio_hora }}" data-desc="{{ htmlspecialchars($c->descripcion ?? '', ENT_QUOTES, 'UTF-8') }}">{{ $c->nombre }} — ${{ number_format($c->precio_hora,0,',','.') }} / hora</option>
                            @endforeach
                        </select>
                    </div>

                    <div style="margin-top:8px;">
                        <div class="small"><strong>Descripción seleccionada:</strong></div>
                        <div id="selected_desc" class="small" style="color:#bfbfbf;margin-top:6px;">—</div>
                    </div>
                </div>

                {{-- Date/time controls --}}
                <div style="width:320px;flex:0 0 320px;">
                    <label class="small" for="fecha">Fecha</label>
                    <input id="fecha" type="date" name="fecha" required class="w-full p-2 border"
                        value="{{ old('fecha') ?? \Carbon\Carbon::now()->toDateString() }}"
                        min="{{ \Carbon\Carbon::now()->toDateString() }}"
                        style="margin-bottom:8px;">

                    <label class="small" for="hora_ui">Hora</label>

                    {{-- UI de hora: hora (1-12), minutos (00,15,30,45), AM/PM --}}
                    <div style="display:flex;gap:6px;margin-bottom:8px;">
                        <select id="hora_hour" class="p-2 border" aria-label="Hora (hora)">
                            @for($h=1;$h<=12;$h++)
                                <option value="{{ $h }}" @if(old('hora') && \Carbon\Carbon::parse(old('hora'))->format('g') == $h) selected @endif>{{ $h }}</option>
                            @endfor
                        </select>

                        <select id="hora_min" class="p-2 border" aria-label="Minutos">
                            @foreach(['00','15','30','45'] as $m)
                                <option value="{{ $m }}" @if(old('hora') && \Carbon\Carbon::parse(old('hora'))->format('i') == $m) selected @endif>{{ $m }}</option>
                            @endforeach
                        </select>

                        <select id="hora_ampm" class="p-2 border" aria-label="AM/PM">
                            <option value="AM" @if(old('hora') && \Carbon\Carbon::parse(old('hora'))->format('A')==='AM') selected @endif>AM</option>
                            <option value="PM" @if(old('hora') && \Carbon\Carbon::parse(old('hora'))->format('A')==='PM') selected @endif>PM</option>
                        </select>
                    </div>

                    {{-- campo oculto real que envía la hora en formato HH:MM (24h) --}}
                    <input type="hidden" name="hora" id="hora_hidden" value="{{ old('hora') ?? '12:00' }}">

                    <label class="small" for="horas">Horas</label>
                    <select id="horas" name="horas" required class="w-full p-2 border" style="margin-bottom:12px;">
                        <option value="1" @selected(old('horas')==1)>1</option>
                        <option value="2" @selected(old('horas')==2)>2</option>
                        <option value="3" @selected(old('horas')==3)>3</option>
                    </select>

                    <input type="hidden" name="consola_id" id="consola_id_hidden" value="{{ old('consola_id') ?? '' }}">

                    <div style="margin-top:6px;">
                        <div style="display:flex;justify-content:space-between;align-items:center;">
                            <div class="small">Precio estimado</div>
                            <div style="font-weight:700;" id="precioEstimado">-</div>
                        </div>
                        <small class="small" style="color:#bfbfbf;">Precio real final será calculado en el servidor según la consola y horas (max 3h).</small>
                    </div>

                    <div style="margin-top:12px;display:flex;gap:8px;align-items:center;">
                        <button type="submit" class="gamer-btn-rect" style="flex:1;">Reservar (queda pendiente)</button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    {{-- Mis reservas --}}
    <div class="card">
        <h2 class="card-title" style="color:var(--neon-cyan);margin:0 0 6px 0;">Mis reservas</h2>
        <div class="small" style="margin-bottom:8px;color:#bfbfbf;">Lista de reservas recientes. Puedes editar o cancelar si están en estado <strong>pending</strong>.</div>

        <div class="table-wrap">
            <table class="table" aria-live="polite">
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
                    @forelse($misReservas as $r)
                        <tr>
                            <td>{{ $r->consola->nombre }}</td>
                            <td>{{ $r->start_at->format('Y-m-d g:i A') }}</td>
                            <td>{{ $r->end_at->format('Y-m-d g:i A') }}</td>
                            <td>{{ $r->horas }}</td>
                            <td>{{ number_format($r->precio_total, 0, ',', '.') }}</td>
                            <td style="text-transform:capitalize;">{{ $r->status }}</td>
                            <td>
                                @if($r->status === 'pending')
                                    <a href="{{ route('reserva.edit', $r) }}" class="btn-ghost mr-1">Editar</a>

                                    <form action="{{ route('reserva.destroy', $r) }}" method="POST" style="display:inline">
                                        @csrf @method('DELETE')
                                        <button class="btn-ghost mr-1" type="submit" onclick="return confirm('¿Cancelar esta reserva?')">Cancelar</button>
                                    </form>

                                    {{-- FORMULARIO QUE INICIA PAGO: ahora abre la vista sandbox (GET) y tiene fallback POST --}}
                                    <form action="{{ route('reserva.pay', $r) }}" method="POST" style="display:inline" class="sandbox-form" data-sandbox-url="{{ route('reserva.sandbox', $r) }}">
                                        @csrf
                                        <button class="neon-btn" type="submit">Pagar (sandbox)</button>
                                    </form>
                                @else
                                    <span class="small">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="small">No hay reservas aún. ¡Haz tu primera reserva!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

{{-- JS inline (tu script para interacción con consolas, precio estimado y hora AM/PM) --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const container = document.querySelector('.consola-grid');
    const botones = container ? Array.from(container.querySelectorAll('.consola-btn')) : [];
    const selectFallback = document.getElementById('consola_select');
    const hiddenId = document.getElementById('consola_id_hidden');
    const precioEstimadoEl = document.getElementById('precioEstimado');
    const descEl = document.getElementById('selected_desc');
    const horasEl = document.getElementById('horas');
    const fechaEl = document.getElementById('fecha');

    const hourSel = document.getElementById('hora_hour');
    const minSel = document.getElementById('hora_min');
    const ampmSel = document.getElementById('hora_ampm');
    const horaHidden = document.getElementById('hora_hidden');

    const precios = {};
    botones.forEach(b => { precios[b.dataset.id] = parseFloat(b.dataset.precio) || 0; });

    function formatNumber(n) { return Number(n).toLocaleString('es-CO'); }

    function marcarSeleccion(id, descripcion) {
        botones.forEach(b => {
            if (String(b.dataset.id) === String(id)) {
                b.classList.add('gamer-btn-rect'); b.classList.remove('card'); b.setAttribute('aria-pressed','true');
            } else {
                b.classList.remove('gamer-btn-rect'); b.classList.add('card'); b.setAttribute('aria-pressed','false');
            }
        });
        hiddenId.value = id || '';
        descEl.textContent = descripcion || '—';
        updatePrecioEstimado();
    }

    function updatePrecioEstimado() {
        const id = hiddenId.value;
        const horas = parseInt(horasEl.value || '1', 10);
        const p = precios[id] || 0;
        const total = p * horas;
        precioEstimadoEl.textContent = (id ? ('$' + formatNumber(total)) : '-');
    }

    function ajustarMinHora() {
        if (!fechaEl) return;
        const selectedDate = new Date(fechaEl.value + 'T00:00:00');
        const today = new Date();
        const todayDate = new Date(today.getFullYear(), today.getMonth(), today.getDate());
        if (selectedDate.getTime() === todayDate.getTime()) {
            const hh = String(today.getHours()).padStart(2,'0');
            const mm = String(today.getMinutes()).padStart(2,'0');
            // Ajuste: si hoy, límite lógico de hora (solo UI: no permite hora pasada)
            // Convert current 24h hh:mm to nearest 15-min block for UI selection
            const currH = today.getHours();
            const currM = Math.floor(today.getMinutes() / 15) * 15;
            // set UI selects to this minimum if lower
            let displayHour = currH % 12 === 0 ? 12 : currH % 12;
            if (parseInt(hourSel.value) < displayHour && ampmSel.value === (currH >= 12 ? 'AM' : 'PM')) {
                hourSel.value = displayHour;
            }
        }
    }

    // sincroniza los selects AM/PM -> campo hora oculto (24h)
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

    // listeners: si cambian selects actualizar hidden
    [hourSel, minSel, ampmSel].forEach(el => {
        if (el) el.addEventListener('change', syncHoraHidden);
    });

    // inicializo horaHidden con valor antiguo si existe
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

    botones.forEach(b => {
        b.addEventListener('click', () => {
            const id = b.dataset.id;
            const desc = b.dataset.desc;
            if (selectFallback) selectFallback.value = id;
            marcarSeleccion(id, desc);
        });
        b.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); b.click(); }
        });
    });

    if (selectFallback) {
        selectFallback.addEventListener('change', () => {
            const opt = selectFallback.options[selectFallback.selectedIndex];
            const id = opt.value;
            const desc = opt.dataset.desc || opt.text;
            marcarSeleccion(id, desc);
        });
    }

    if (horasEl) horasEl.addEventListener('change', updatePrecioEstimado);
    if (fechaEl) fechaEl.addEventListener('change', ajustarMinHora);

    const initId = "{{ old('consola_id') ?? '' }}";
    const initHoras = "{{ old('horas') ?? 1 }}";
    if (horasEl && initHoras) horasEl.value = initHoras;
    if (initId) {
        const btnInit = botones.find(b => String(b.dataset.id) === String(initId));
        if (btnInit) { btnInit.click(); }
        else if (selectFallback) {
            const opt = selectFallback.querySelector('option[value="'+initId+'"]');
            if (opt) { selectFallback.value = initId; marcarSeleccion(initId, opt.dataset.desc || ''); }
        }
    } else {
        const first = botones[0];
        if (first) descEl.textContent = first.dataset.desc || '—';
    }

    ajustarMinHora();
    updatePrecioEstimado();

    // Interceptar los forms de pago para abrir la vista sandbox en vez de hacer POST directo
    document.querySelectorAll('.sandbox-form').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const url = form.dataset.sandboxUrl;
            if (url) {
                // redirigimos al GET que mostrará la simulación sandbox
                window.location.href = url;
            } else {
                // fallback: si algo falla, envía el form
                form.submit();
            }
        });
    });

    // Antes de enviar el form de reserva, aseguro que el campo hora_hidden esté sincronizado
    const reservaForm = document.getElementById('reservaForm');
    if (reservaForm) {
        reservaForm.addEventListener('submit', function () {
            syncHoraHidden();
        });
    }
});
</script>

@endsection
