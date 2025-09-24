<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Reserva;
use App\Models\Consola;
use Carbon\Carbon;

class ReservaController extends Controller
{
    public function index()
    {
        $consolas = Consola::all();
        $misReservas = Auth::user()->reservas()->orderBy('start_at', 'desc')->get();

        return view('reserva.index', compact('consolas', 'misReservas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'consola_id' => 'required|exists:consolas,id',
            'fecha'      => 'required|date',
            'hora'       => 'required',
            'horas'      => 'required|integer|min:1|max:3',
        ]);

        $consola = Consola::findOrFail($request->consola_id);

        $startAt = Carbon::parse($request->fecha . ' ' . $request->hora);
        $horasEntero = (int) $request->horas;
        $endAt = (clone $startAt)->addHours($horasEntero);

        $now = Carbon::now();
        if ($startAt->lt($now)) {
            return back()->withErrors(['fecha' => 'No puedes reservar en una fecha/hora pasada.'])->withInput();
        }

        if (Reserva::overlaps($consola->id, $startAt, $endAt)) {
            return back()->withErrors(['conflict' => 'La consola ya está reservada y pagada en ese horario.'])->withInput();
        }

        $precioTotal = round($consola->precio_hora * $horasEntero, 2);

        $reserva = Reserva::create([
            'user_id' => Auth::id(),
            'consola_id' => $consola->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'horas' => $horasEntero,
            'precio_total' => $precioTotal,
            'status' => 'pending',
        ]);

        return redirect()->route('reserva.index')->with('success', 'Reserva creada. Recuerda pagar para confirmar.');
    }

    public function edit(Reserva $reserva)
    {
        if ($reserva->user_id !== Auth::id() || $reserva->status !== 'pending') {
            return redirect()->route('reserva.index')->withErrors('No puedes editar esta reserva.');
        }

        $consolas = Consola::all();

        return view('reserva.edit', compact('reserva', 'consolas'));
    }

    public function update(Request $request, Reserva $reserva)
    {
        if ($reserva->user_id !== Auth::id() || $reserva->status !== 'pending') {
            return redirect()->route('reserva.index')->withErrors('No puedes editar esta reserva.');
        }

        $request->validate([
            'consola_id' => 'required|exists:consolas,id',
            'fecha'      => 'required|date',
            'hora'       => 'required',
            'horas'      => 'required|integer|min:1|max:3',
        ]);

        $consola = Consola::findOrFail($request->consola_id);
        $startAt = Carbon::parse($request->fecha . ' ' . $request->hora);
        $horasEntero = (int) $request->horas;
        $endAt = (clone $startAt)->addHours($horasEntero);

        $now = Carbon::now();
        if ($startAt->lt($now)) {
            return back()->withErrors(['fecha' => 'No puedes actualizar a una fecha/hora pasada.'])->withInput();
        }

        if (Reserva::where('id', '!=', $reserva->id)
            ->where('consola_id', $consola->id)
            ->where('status', 'paid')
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->exists()
        ) {
            return back()->withErrors(['conflict' => 'Horario no disponible (existente reserva pagada).'])->withInput();
        }

        $reserva->update([
            'consola_id' => $consola->id,
            'start_at' => $startAt,
            'end_at' => $endAt,
            'horas' => $horasEntero,
            'precio_total' => round($consola->precio_hora * $horasEntero, 2),
        ]);

        return redirect()->route('reserva.index')->with('success', 'Reserva actualizada.');
    }

    public function destroy(Reserva $reserva)
    {
        if ($reserva->user_id !== Auth::id() && !Auth::user()->isAdmin()) {
            abort(403);
        }

        if (Auth::user()->isAdmin()) {
            $reserva->delete();
            return back()->with('success', 'Reserva eliminada por admin.');
        }

        if ($reserva->status === 'paid') {
            return back()->withErrors('No puedes cancelar una reserva pagada. Contacta al admin.');
        }

        $reserva->update(['status' => 'canceled']);
        return back()->with('success', 'Reserva cancelada.');
    }

    public function pay(Reserva $reserva)
    {
        if ($reserva->user_id !== Auth::id()) abort(403);
        if ($reserva->status !== 'pending') return back()->withErrors('Reserva no está en estado pendiente.');

        $reserva->update(['status' => 'paid']);

        return redirect()->route('reserva.index')->with('success', 'Pago simulado: reserva marcada como pagada.');
    }

    /* ---------- ADMIN ---------- */

    public function adminIndex()
    {
        // Aseguramos que solo admin acceda
        if (! Auth::user() || ! Auth::user()->isAdmin()) {
            abort(403);
        }

        $reservas = Reserva::with('user', 'consola')->orderBy('start_at', 'desc')->get();

        $totalGanancias = $reservas->where('status', 'paid')->sum('precio_total');
        $totalPendientes = $reservas->where('status', 'pending')->sum('precio_total');
        $pagadas = $reservas->where('status', 'paid')->count();
        $pendientes = $reservas->where('status', 'pending')->count();

        // Si la vista blade existe, la usamos (comportamiento normal)
        if (view()->exists('admin.reservas')) {
            return view('admin.reservas', compact('reservas', 'totalGanancias', 'totalPendientes', 'pagadas', 'pendientes'));
        }

        // FALLBACK (temporal): si la vista no existe, devolvemos HTML básico para que sigas trabajando.
        $html = '<!doctype html><html><head><meta charset="utf-8"><title>Admin - Reservas (fallback)</title></head><body style="font-family: Arial, sans-serif; padding:20px;">';
        $html .= '<h1>Panel Admin - Reservas (fallback)</h1>';
        $html .= '<p style="color:darkred">Nota: la vista <strong>admin.reservas</strong> no fue encontrada. Muestra de datos en fallback.</p>';
        $html .= '<div><strong>Total ganancias (pagadas):</strong> $'.number_format($totalGanancias ?? 0,0,',','.').'</div>';
        $html .= '<div><strong>Total pendientes:</strong> $'.number_format($totalPendientes ?? 0,0,',','.').'</div>';
        $html .= '<div style="margin-top:10px;"><table border="1" cellpadding="6" cellspacing="0"><thead><tr><th>Usuario</th><th>Consola</th><th>Inicio</th><th>Fin</th><th>Precio</th><th>Estado</th></tr></thead><tbody>';
        foreach ($reservas as $r) {
            $html .= '<tr>';
            $html .= '<td>'.htmlspecialchars($r->user->name ?? '—').' <br><small>'.htmlspecialchars($r->user->email ?? '').'</small></td>';
            $html .= '<td>'.htmlspecialchars($r->consola->nombre ?? '—').'</td>';
            $html .= '<td>'.optional($r->start_at)->format('Y-m-d H:i').'</td>';
            $html .= '<td>'.optional($r->end_at)->format('Y-m-d H:i').'</td>';
            $html .= '<td>$'.number_format($r->precio_total ?? 0,0,',','.').'</td>';
            $html .= '<td>'.htmlspecialchars(ucfirst($r->status ?? '—')).'</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table></div>';
        $html .= '<p style="margin-top:12px;color:#666">Crea <code>resources/views/admin/reservas.blade.php</code> para usar la vista con layout.</p>';
        $html .= '</body></html>';

        return response($html, 200)->header('Content-Type', 'text/html');
    }

    public function adminMarkPaid(Reserva $reserva)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $reserva->update(['status' => 'paid']);
        return back()->with('success', 'Reserva marcada como pagada.');
    }

    public function adminUpdateConsolaPrice(Request $request, Consola $consola)
    {
        if (!Auth::user()->isAdmin()) abort(403);
        $request->validate(['precio_hora' => 'required|numeric|min:0']);
        $consola->update(['precio_hora' => $request->precio_hora]);
        return back()->with('success', 'Precio de consola actualizado.');
    }
}
