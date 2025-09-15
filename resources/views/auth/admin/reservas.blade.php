<x-app-layout>
    <div class="container p-6 bg-[#0d0d0d] text-white rounded-lg shadow-lg">
        <h1 class="text-3xl font-bold mb-4 text-[#00ffcc]">📋 Reservas (Admin)</h1>

        {{-- Mensajes --}}
        @if(session('success'))
            <div class="mb-3 bg-green-800 text-green-200 p-2 rounded">{{ session('success') }}</div>
        @endif

        {{-- Tabla de reservas --}}
        <div class="overflow-x-auto">
            <table class="w-full table-auto border border-[#00ffcc66] rounded-xl overflow-hidden shadow-md">
                <thead>
                    <tr class="bg-[#00ffcc33] text-[#00ffcc]">
                        <th class="p-2 text-left">Usuario</th>
                        <th class="p-2 text-left">Consola</th>
                        <th class="p-2 text-left">Fecha</th>
                        <th class="p-2 text-left">Hora</th>
                        <th class="p-2 text-right">Precio</th>
                        <th class="p-2 text-left">Estado</th>
                        <th class="p-2 text-center">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reservas as $r)
                        <tr class="border-t border-[#00ffcc33] hover:bg-[#1a1a1a] transition">
                            <td class="p-2">
                                {{ optional($r->user)->name ?? '—' }}
                                <div class="text-xs text-gray-400">{{ optional($r->user)->email ?? '' }}</div>
                            </td>
                            <td class="p-2">{{ optional($r->console)->name ?? '—' }}</td>
                            <td class="p-2">{{ $r->date }}</td>
                            <td class="p-2">
                                @php
                                    try { $start = \Carbon\Carbon::parse($r->start_time)->format('H:i'); }
                                    catch (\Throwable $e) { $start = $r->start_time; }
                                    try { $end = \Carbon\Carbon::parse($r->end_time)->format('H:i'); }
                                    catch (\Throwable $e) { $end = $r->end_time; }
                                @endphp
                                {{ $start }} - {{ $end }}
                            </td>
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
                            <td class="p-2 text-center">
                                @if($r->status === 'paid')
                                    <button class="gamer-btn-rect opacity-50 cursor-not-allowed">Eliminar</button>
                                    <div class="text-xs text-red-600 mt-1">Pagada — no editable</div>
                                @else
                                    <form method="POST" action="{{ route('admin.reservas.destroy', $r) }}" onsubmit="return confirm('¿Eliminar reserva?');" style="display:inline;">
                                        @csrf
                                        @method('DELETE')
                                        <button class="gamer-btn-rect bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded-lg">Eliminar</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-4 text-center text-gray-400">No tienes reservas.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
