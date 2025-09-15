<x-app-layout>
    <div class="container p-6 bg-[#0d0d0d] text-white rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-4 text-[#00ffcc]">🎮 Mis Reservas</h1>

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="mb-3 bg-green-800 text-green-200 p-2 rounded">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="mb-3 bg-red-800 text-red-200 p-2 rounded">
                @foreach($errors->all() as $error)
                    <div>⚠️ {{ $error }}</div>
                @endforeach
            </div>
        @endif

        {{-- Formulario de reserva --}}
        <form action="{{ route('reserva.store') }}" method="POST" class="mb-6 border border-[#00ffcc66] bg-[#1a1a1a] p-4 rounded-xl shadow-md">
            @csrf
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="console_id" class="block font-semibold text-[#00ffcc]">Consola</label>
                    <select name="console_id" id="console_id" class="w-full border border-[#00ffcc] bg-[#1a1a1a] text-white p-2 rounded">
                        @foreach($consoles as $console)
                            <option value="{{ $console->id }}" {{ old('console_id') == $console->id ? 'selected' : '' }}>
                                {{ $console->name }} ({{ number_format($console->price_per_hour) }} COP/h)
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="date" class="block font-semibold text-[#00ffcc]">Fecha</label>
                    <input type="date" name="date" id="date"
                        value="{{ old('date') }}"
                        class="w-full border border-[#00ffcc] bg-[#1a1a1a] text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-[#00ffcc]">
                </div>

                <div>
                    <label for="start_time" class="block font-semibold text-[#00ffcc]">Hora inicio</label>
                    <input type="time" name="start_time" id="start_time"
                        value="{{ old('start_time') }}"
                        class="w-full border border-[#00ffcc] bg-[#1a1a1a] text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-[#00ffcc]">
                </div>

                <div>
                    <label for="end_time" class="block font-semibold text-[#00ffcc]">Hora fin</label>
                    <!-- readonly para que no pueda editarse, pero sí se envía en el form -->
                    <input type="time" name="end_time" id="end_time"
                        value="{{ old('end_time') }}"
                        readonly
                        class="w-full border border-[#00ffcc] bg-[#1a1a1a] text-white p-2 rounded focus:outline-none focus:ring-2 focus:ring-[#00ffcc] cursor-not-allowed">
                </div>
            </div>

            <div class="mt-4 flex items-center gap-4">
                <button type="submit" class="gamer-btn-rect bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-xl transition">
                    ➕ Reservar
                </button>

                <div id="preview_price" class="text-[#00ffcc] font-semibold"></div>
            </div>
        </form>

        {{-- Tabla de reservas del usuario --}}
        <div class="overflow-x-auto">
            <table class="w-full table-auto border border-[#00ffcc66] rounded-xl overflow-hidden shadow-md">
                <thead>
                    <tr class="bg-[#00ffcc33] text-[#00ffcc]">
                        <th class="p-2 text-left">Consola</th>
                        <th class="p-2 text-left">Fecha</th>
                        <th class="p-2 text-left">Hora</th>
                        <th class="p-2 text-right">Precio</th>
                        <th class="p-2 text-left">Estado</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($userReservas as $r)
                        <tr class="border-t border-[#00ffcc33] hover:bg-[#1a1a1a] transition">
                            <td class="p-2">{{ optional($r->console)->name ?? '—' }}</td>
                            <td class="p-2">{{ $r->date }}</td>
                            <td class="p-2">{{ $r->start_time }} - {{ $r->end_time }}</td>
                            <td class="p-2 text-right text-[#00ffcc] font-semibold">{{ number_format($r->total_price) }} COP</td>
                            <td class="p-2">
                                @if($r->status === 'pending')
                                    <span class="px-2 py-1 bg-yellow-700 text-yellow-200 rounded">Pendiente</span>
                                @elseif($r->status === 'paid')
                                    <span class="px-2 py-1 bg-green-700 text-green-200 rounded">Pagada</span>
                                @else
                                    <span class="px-2 py-1 bg-gray-700 text-gray-200 rounded">{{ ucfirst($r->status) }}</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-4 text-center text-gray-400">No tienes reservas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Script para asignar hora automática y bloquear edición de end_time --}}
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            const dateInput = document.getElementById('date');
            const startInput = document.getElementById('start_time');
            const endInput = document.getElementById('end_time');
            const consoleSelect = document.getElementById('console_id');
            const previewPrice = document.getElementById('preview_price');
            const DURATION_MINUTES = 60; // duración por defecto en minutos (cambia si quieres)

            const pad = n => n.toString().padStart(2, '0');
            const formatTime = d => pad(d.getHours()) + ':' + pad(d.getMinutes());
            const todayYMD = () => new Date().toISOString().split('T')[0];

            function setDefaultsIfEmpty() {
                const now = new Date();

                // Fecha: solo si no hay valor (para respetar old() y volver desde errores)
                if (!dateInput.value) {
                    dateInput.value = todayYMD();
                }
                // min de la fecha = hoy
                dateInput.min = todayYMD();

                // Hora inicio: solo si no hay value (respetar old)
                if (!startInput.value) {
                    startInput.value = formatTime(now);
                }

                // Hora fin = start + DURATION (si está vacío o si el end es <= start recalculamos)
                recalcEndFromStart();
                updateStartMinIfToday();
                previewPriceUpdate();
            }

            function recalcEndFromStart() {
                if (!startInput.value) return;
                const parts = startInput.value.split(':').map(x => parseInt(x,10));
                const s = new Date();
                s.setHours(parts[0], parts[1], 0, 0);
                const e = new Date(s.getTime() + DURATION_MINUTES * 60000);
                endInput.value = formatTime(e);
            }

            function updateStartMinIfToday() {
                const now = new Date();
                if (dateInput.value === todayYMD()) {
                    startInput.min = formatTime(now);
                    // si el start actual es menor al min, ponerlo al min
                    if (startInput.value && startInput.value < startInput.min) {
                        startInput.value = startInput.min;
                        recalcEndFromStart();
                    }
                } else {
                    startInput.removeAttribute('min');
                }
            }

            // cuando el usuario cambia start_time manualmente -> recalc end_time
            startInput.addEventListener('change', () => {
                // si el usuario eligió una hora menor que el minimo (si es hoy) la forzamos
                if (startInput.min && startInput.value < startInput.min) {
                    startInput.value = startInput.min;
                }
                recalcEndFromStart();
                previewPriceUpdate();
            });

            // si el usuario cambia la fecha -> actualizar min de start y recalcular end
            dateInput.addEventListener('change', () => {
                updateStartMinIfToday();
                recalcEndFromStart();
            });

            // calcular precio preliminar (opcional). Busca el precio por hora en el option seleccionado
            function previewPriceUpdate() {
                if (!consoleSelect) return;
                const opt = consoleSelect.options[consoleSelect.selectedIndex];
                if (!opt) { previewPrice.textContent = ''; return; }

                // asumimos que en el texto option está como "(5,000 COP/h)". 
                // Si prefieres, agrega data-price en el option para extraerlo mejor.
                // Ejemplo: <option data-price="{{ $console->price_per_hour }}" ...>
                const pricePerHour = opt.getAttribute('data-price') ? parseFloat(opt.getAttribute('data-price')) : null;
                if (!pricePerHour) {
                    previewPrice.textContent = '';
                    return;
                }

                // calcular minutos entre start y end (sin fecha)
                const [sh, sm] = startInput.value.split(':').map(x => parseInt(x, 10));
                const [eh, em] = endInput.value.split(':').map(x => parseInt(x, 10));
                // tiempo en minutos (si end <= start asumimos next day)
                let startDate = new Date(); startDate.setHours(sh, sm, 0, 0);
                let endDate = new Date(); endDate.setHours(eh, em, 0, 0);
                if (endDate <= startDate) endDate.setDate(endDate.getDate() + 1);
                const minutes = Math.round((endDate - startDate) / 60000);
                const hours = minutes / 60;
                const total = Math.round(hours * pricePerHour);
                previewPrice.textContent = `Total estimado: ${new Intl.NumberFormat('es-CO').format(total)} COP`;
            }

            // si tus <option> tienen data-price, previewPriceUpdate funciona; si no, puedes añadir data-price="{{ $console->price_per_hour }}" en el blade
            setDefaultsIfEmpty();

            // Si el usuario vuelve por errores con old() los valores se mantienen porque setDefaultsIfEmpty respeta old.
        });
    </script>
</x-app-layout>
