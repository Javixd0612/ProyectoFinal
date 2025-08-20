<!-- resources/views/reservas/create.blade.php -->
<x-app-layout>
    <main class="page-bg">
        <div class="py-12 page-wrapper">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 content-area">
                <div class="card">
                    <div class="p-6">
                        <h2 class="card-title">Reservar consola</h2>

                        @if($errors->any())
                          <div class="alert-error">
                            <ul>@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
                          </div>
                        @endif

                        <form action="{{ route('reservas.store') }}" method="POST" id="reservaForm">
                          @csrf

                          <div class="form-group">
                            <label>Consola</label>
                            <select name="consola_id" id="consolaSelect" required>
                              <option value="">-- Selecciona consola --</option>
                              @foreach($consolas as $c)
                                <option value="{{ $c->id }}" data-nombre="{{ $c->nombre }}">{{ $c->nombre }}</option>
                              @endforeach
                            </select>
                          </div>

                          <div class="form-group">
                            <label>Fecha</label>
                            <input type="date" name="fecha" id="fechaInput" min="{{ date('Y-m-d') }}" required>
                          </div>

                          <div style="display:flex;gap:.6rem;">
                            <div class="form-group" style="flex:1">
                              <label>Hora inicio</label>
                              <input type="time" name="hora_inicio" id="horaInicio" required>
                            </div>
                            <div class="form-group" style="flex:1">
                              <label>Hora fin</label>
                              <input type="time" name="hora_fin" id="horaFin" required>
                            </div>
                          </div>

                          <div class="form-group">
                            <label>Precio estimado</label>
                            <div id="precioEstimado" class="card small">--</div>
                          </div>

                          <div>
                            <button class="neon-btn" type="submit">Reservar</button>
                          </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function(){
      const precios = @json($precioPorConsola ?? []);
      const consolaSelect = document.getElementById('consolaSelect');
      const horaInicio = document.getElementById('horaInicio');
      const horaFin = document.getElementById('horaFin');
      const precioEstimadoEl = document.getElementById('precioEstimado');

      function setHoraFinDefault(){
        if (!horaInicio.value) return;
        const [h,m] = horaInicio.value.split(':').map(Number);
        const dt = new Date();
        dt.setHours(h, m || 0, 0);
        dt.setHours(dt.getHours()+1);
        horaFin.value = String(dt.getHours()).padStart(2,'0') + ':' + String(dt.getMinutes()).padStart(2,'0');
      }

      function calcularPrecio(){
        const opt = consolaSelect.options[consolaSelect.selectedIndex];
        if (!opt || !opt.value) { precioEstimadoEl.innerText = '--'; return; }
        const nombre = opt.dataset.nombre;
        const h1 = horaInicio.value, h2 = horaFin.value;
        if (!h1 || !h2) { precioEstimadoEl.innerText = '--'; return; }
        const [h1h,h1m] = h1.split(':').map(Number);
        const [h2h,h2m] = h2.split(':').map(Number);
        const t1 = h1h*60+h1m, t2 = h2h*60+h2m;
        if (t2<=t1) { precioEstimadoEl.innerText = 'Hora fin debe ser mayor'; return; }
        const minutos = t2-t1;
        if (minutos<30) { precioEstimadoEl.innerText = 'Min. 30 minutos'; return; }
        const horas = minutos/60;
        const precioHora = precios[nombre] ?? Object.values(precios)[0] ?? 0;
        precioEstimadoEl.innerText = '$ ' + Math.round(precioHora * horas).toLocaleString('es-CO');
      }

      consolaSelect.addEventListener('change', calcularPrecio);
      horaInicio.addEventListener('change', function(){ setHoraFinDefault(); calcularPrecio(); });
      horaFin.addEventListener('change', calcularPrecio);
    });
    </script>
</x-app-layout>
