<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Console;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    // Vista usuario (solo clientes, no admin)
    public function index()
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.reservas')
                ->with('error', 'Los administradores no pueden acceder a la interfaz de reservas.');
        }

        $consoles = Console::all();
        $userReservas = Auth::user()->reservas()
            ->orderByDesc('date')
            ->orderByDesc('start_time')
            ->get();

        return view('reserva.index', compact('consoles', 'userReservas'));
    }

    // Guardar reserva (solo clientes)
    public function store(Request $request)
    {
        if (Auth::check() && Auth::user()->isAdmin()) {
            return redirect()->route('admin.reservas')
                ->with('error', 'Los administradores no pueden crear reservas.');
        }

        $request->validate([
            'console_id' => ['required','exists:consoles,id'],
            'date'       => ['required','date','after_or_equal:today'],
            'start_time' => ['required','date_format:H:i'],
        ]);

        $user = Auth::user();
        if (!$user) {
            return redirect()->route('login');
        }

        // Verificar si ya tiene reserva activa
        $hasActive = Reserva::where('user_id', $user->id)
            ->whereIn('status',['pending','paid'])
            ->whereDate('date', '>=', now()->toDateString())
            ->exists();

        if ($hasActive) {
            return back()->withErrors(['Ya tienes una reserva activa.'])->withInput();
        }

        $date  = $request->input('date');
        $start = $request->input('start_time');

        // Generar hora fin automáticamente (+1 hora)
        $startCarbon = Carbon::createFromFormat('H:i', $start);
        $endCarbon   = $startCarbon->copy()->addHour();
        $end         = $endCarbon->format('H:i');

        // Validar solapamiento
        $overlap = Reserva::where('console_id', $request->console_id)
            ->where('date', $date)
            ->where(function($q) use ($start, $end) {
                $q->where('start_time', '<', $end)
                  ->where('end_time', '>', $start);
            })->exists();

        if ($overlap) {
            return back()->withErrors(['El horario ya está ocupado para esa consola.'])->withInput();
        }

        // Calcular costo (siempre 1h)
        $console = Console::findOrFail($request->console_id);
        $total   = intval(round($console->price_per_hour));

        Reserva::create([
            'user_id'     => $user->id,
            'console_id'  => $console->id,
            'date'        => $date,
            'start_time'  => $start,
            'end_time'    => $end,
            'total_price' => $total,
            'status'      => 'pending',
        ]);

        return redirect()->route('reserva.index')
            ->with('success', 'Reserva creada correctamente. Total: '.number_format($total).' COP');
    }

    // Vista admin: todas las reservas
    public function adminIndex()
    {
        $reservas = Reserva::with('user','console')
            ->orderBy('date','desc')
            ->orderBy('start_time','desc')
            ->get();

        return view('admin.reservas', compact('reservas'));
    }

    // Admin: eliminar reserva
    public function destroy(Reserva $reserva)
    {
        if ($reserva->status === 'paid') {
            return back()->withErrors(['Esta reserva ya fue pagada y no puede eliminarse.']);
        }

        $reserva->delete();
        return back()->with('success','Reserva eliminada.');
    }
}
