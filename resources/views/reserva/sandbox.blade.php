{{-- resources/views/reserva/sandbox.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="sandbox-wrap" style="min-height:70vh;display:flex;align-items:center;justify-content:center;padding:40px 16px;">
    <div class="sandbox-card" style="width:920px;max-width:95%;border-radius:14px;box-shadow:0 12px 40px rgba(0,0,0,0.6);overflow:hidden;background:linear-gradient(180deg,#0b0b0b,#0f1112);border:1px solid rgba(0,255,204,0.05);">
        <div style="padding:28px 28px 10px 28px;display:flex;align-items:center;justify-content:space-between;gap:16px;">
            <div>
                <h2 style="margin:0;font-size:20px;color:var(--neon-cyan);">Sandbox: Pago simulado</h2>
                <div style="color:#bfbfbf;font-size:13px;margin-top:6px;">Simulación visual del proceso de pago. Esto no realiza cargos reales.</div>
            </div>
            <div style="font-size:13px;color:#bfbfbf;">
                Reserva: <strong>#{{ $reserva->id }}</strong>
            </div>
        </div>

        <div style="display:flex;gap:20px;padding:18px 28px 28px;">
            {{-- LEFT: loader / estado --}}
            <div style="flex:0 0 360px; min-height:300px; display:flex;flex-direction:column;align-items:center;justify-content:center;gap:14px;border-radius:10px;padding:18px;background:linear-gradient(180deg, rgba(255,255,255,0.02), rgba(255,255,255,0.01));">
                <div id="loaderStage" style="display:none;flex-direction:column;align-items:center;gap:12px;">
                    <div id="captchaBox" style="width:200px;height:120px;border-radius:10px;display:flex;align-items:center;justify-content:center;background:#071010;border:1px dashed rgba(0,255,204,0.06);">
                        <div id="captchaText" style="text-align:center;color:#dff9ef;font-weight:700;">Verificando...</div>
                    </div>

                    <div id="progressText" style="color:#bfbfbf;font-size:13px;">Espere mientras validamos su pago.</div>

                    <div style="width:140px;height:140px;display:flex;align-items:center;justify-content:center;">
                        <svg id="spinner" width="80" height="80" viewBox="0 0 50 50" xmlns="http://www.w3.org/2000/svg">
                            <path fill="none" stroke="rgba(0,255,204,0.18)" stroke-width="4" d="M25 5 A20 20 0 0 1 45 25" />
                            <path id="spinnerArc" fill="none" stroke="var(--neon-cyan)" stroke-width="4" stroke-linecap="round" d="M25 5 A20 20 0 0 1 45 25" />
                        </svg>
                    </div>
                </div>

                <div id="successBox" style="display:none;flex-direction:column;align-items:center;gap:8px;">
                    <div style="width:90px;height:90px;border-radius:999px;background:linear-gradient(180deg,#042,#026);display:flex;align-items:center;justify-content:center;border:2px solid rgba(0,255,204,0.12);">
                        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M20 6L9 17l-5-5"/></svg>
                    </div>
                    <div style="font-weight:700;color:#bffadf;">Pago exitoso</div>
                    <div style="color:#bfbfbf;font-size:13px;">Se ha procesado la transacción (sandbox).</div>
                </div>

                {{-- Información y control inicial --}}
                <div id="preBox" style="display:flex;flex-direction:column;align-items:center;gap:10px;">
                    <div style="color:#bfbfbf;font-size:13px;text-align:center;">Selecciona el método de pago y luego pulsa <strong>Iniciar pago</strong> para simular la transacción.</div>
                    <div style="display:flex;flex-direction:column;gap:8px;margin-top:8px;">
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="radio" name="method" value="mercadopago"> Mercado Pago
                        </label>
                        <label style="display:flex;align-items:center;gap:8px;cursor:pointer;">
                            <input type="radio" name="method" value="bancolombia"> Bancolombia
                        </label>
                    </div>

                    <button id="startPayBtn" style="margin-top:12px;padding:10px 14px;border-radius:8px;border:1px solid rgba(0,255,204,0.08);background:transparent;color:var(--neon-cyan);cursor:not-allowed;" disabled>Iniciar pago</button>
                </div>
            </div>

            {{-- RIGHT: factura --}}
            <div style="flex:1;min-height:300px;border-radius:10px;padding:18px;background:linear-gradient(180deg, rgba(255,255,255,0.01), rgba(255,255,255,0.00));border-left:1px solid rgba(0,255,204,0.02);display:flex;flex-direction:column;justify-content:space-between;">
                <div>
                    <div style="display:flex;justify-content:space-between;align-items:flex-start;gap:12px;">
                        <div>
                            <div style="font-size:13px;color:#bfbfbf;margin-bottom:6px;">Factura (simulada)</div>
                            <div style="font-weight:800;font-size:18px;color:var(--neon-cyan);margin-top:6px;">TecnoJuegos - Factura de pago</div>
                        </div>
                        <div style="text-align:right;color:#bfbfbf;font-size:13px;">
                            <div>Fecha: <strong>{{ \Carbon\Carbon::now()->format('Y-m-d') }}</strong></div>
                            <div style="margin-top:6px;">Hora: <strong>{{ \Carbon\Carbon::now()->format('g:i A') }}</strong></div>
                        </div>
                    </div>

                    <hr style="border:none;border-top:1px solid rgba(255,255,255,0.03);margin:12px 0;">

                    <div style="display:flex;gap:12px;flex-direction:column;">
                        <div style="display:flex;justify-content:space-between;">
                            <div class="small" style="color:#bfbfbf;">Usuario</div>
                            <div style="font-weight:700;">{{ optional($reserva->user)->name ?? '—' }}</div>
                        </div>

                        <div style="display:flex;justify-content:space-between;margin-top:6px;">
                            <div class="small" style="color:#bfbfbf;">Consola</div>
                            <div style="font-weight:700;">{{ optional($consola)->nombre ?? '—' }}</div>
                        </div>

                        <div style="display:flex;justify-content:space-between;margin-top:6px;">
                            <div class="small" style="color:#bfbfbf;">Inicio</div>
                            <div style="font-weight:700;">{{ $reserva->start_at->format('Y-m-d g:i A') }}</div>
                        </div>

                        <div style="display:flex;justify-content:space-between;margin-top:6px;">
                            <div class="small" style="color:#bfbfbf;">Fin</div>
                            <div style="font-weight:700;">{{ $reserva->end_at->format('Y-m-d g:i A') }}</div>
                        </div>

                        <div style="display:flex;justify-content:space-between;margin-top:12px;padding:12px;border-radius:8px;background:rgba(0,0,0,0.25);">
                            <div style="color:#bfbfbf;">Precio total</div>
                            <div style="font-weight:800;font-size:18px;color:var(--neon-cyan);">${{ number_format($reserva->precio_total, 0, ',', '.') }}</div>
                        </div>
                    </div>

                    <hr style="border:none;border-top:1px solid rgba(255,255,255,0.03);margin:12px 0;">
                    <div style="color:#bfbfbf;font-size:13px;">
                        Método seleccionado: <strong id="methodLabel">—</strong>
                    </div>
                </div>

                <div style="display:flex;justify-content:space-between;align-items:center;margin-top:18px;">
                    <a href="{{ route('reserva.index') }}" class="btn-ghost" style="padding:10px 14px;">Volver a Reservas</a>

                    {{-- Form que realiza el POST real al controlador pay (marca la reserva como pagada) --}}
                    <form id="confirmPayForm" action="{{ route('reserva.pay', $reserva) }}" method="POST" style="display:inline;">
                        @csrf
                        <input type="hidden" name="method_used" id="method_used" value="">
                        <button id="finalizeBtn" type="submit" class="gamer-btn-rect" style="padding:10px 16px; background:linear-gradient(180deg,#006;#004); border:none;" disabled>Finalizar y marcar como pagado</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Inline JS para animación y sincronización --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    const preBox = document.getElementById('preBox');
    const loaderStage = document.getElementById('loaderStage');
    const successBox = document.getElementById('successBox');
    const startPayBtn = document.getElementById('startPayBtn');
    const finalizeBtn = document.getElementById('finalizeBtn');
    const methodInputs = document.querySelectorAll('input[name="method"]');
    const methodHidden = document.getElementById('method_used');
    const methodLabel = document.getElementById('methodLabel');

    // spinner
    const spinnerArc = document.getElementById('spinnerArc');
    let spinInterval = null;

    // Habilita Iniciar pago solo cuando el usuario elija método
    function updateMethodSelection() {
        let chosen = null;
        methodInputs.forEach(i => { if (i.checked) chosen = i.value; });
        if (chosen) {
            startPayBtn.disabled = false;
            startPayBtn.style.cursor = 'pointer';
            startPayBtn.style.opacity = '1';
            methodHidden.value = chosen;
            methodLabel.textContent = chosen === 'mercadopago' ? 'Mercado Pago' : (chosen === 'bancolombia' ? 'Bancolombia' : chosen);
        } else {
            startPayBtn.disabled = true;
            startPayBtn.style.cursor = 'not-allowed';
            startPayBtn.style.opacity = '0.7';
            methodHidden.value = '';
            methodLabel.textContent = '—';
        }
    }
    methodInputs.forEach(i => i.addEventListener('change', updateMethodSelection));
    updateMethodSelection();

    // Al pulsar Iniciar pago -> mostramos animación (loaderStage) y deshabilitamos preBox
    startPayBtn.addEventListener('click', function (e) {
        e.preventDefault();
        // ocultar preBox, mostrar loader
        preBox.style.display = 'none';
        loaderStage.style.display = 'flex';

        // spinner anim
        let angle = 0;
        spinInterval = setInterval(() => {
            angle = (angle + 8) % 360;
            spinnerArc.setAttribute('transform', `rotate(${angle} 25 25)`);
        }, 30);

        // tiempos: mostrar "Realizando pago..." y luego success
        setTimeout(() => {
            const captchaText = document.getElementById('captchaText');
            const progressText = document.getElementById('progressText');
            captchaText.textContent = 'Realizando pago...';
            progressText.textContent = 'Conectando al procesador (sandbox)';
        }, 600);

        setTimeout(() => {
            clearInterval(spinInterval);
            loaderStage.style.display = 'none';
            successBox.style.display = 'flex';
            // habilitar botón Finalizar
            finalizeBtn.disabled = false;
            finalizeBtn.style.cursor = 'pointer';
        }, 2200);
    });

    // Cuando se haga submit del form final (POST), deshabilitamos el botón para evitar doble envío
    const confirmForm = document.getElementById('confirmPayForm');
    confirmForm.addEventListener('submit', function () {
        finalizeBtn.disabled = true;
        finalizeBtn.textContent = 'Finalizando...';
    });
});
</script>

@endsection
