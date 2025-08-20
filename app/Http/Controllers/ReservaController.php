<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use App\Models\Consola;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class ReservaController extends Controller
{
    // Mostrar formulario de reserva
    public function create()
    {
        $consolas = Consola::where('disponible', true)->orderBy('nombre')->get();

        $precioPorConsola = [
            'PS4' => 3500,
            'PS5' => 5000,
            'Xbox One' => 3500,
            'Xbox Series X' => 4000,
            'Xbox 360' => 3000,
        ];

        return view('reservas.create', compact('consolas','precioPorConsola'));
    }

    // Guardar nueva reserva (evita más de 1 reserva activa por usuario)
    public function store(Request $request)
    {
        $request->validate([
            'consola_id' => 'required|exists:consolas,id',
            'fecha' => 'required|date|after_or_equal:today',
            'hora_inicio' => 'required',
            'hora_fin' => 'required|after:hora_inicio',
        ]);

        // 1) ¿Usuario ya tiene reserva activa (pendiente o pagada) desde hoy?
        $tieneActiva = Reserva::where('user_id', Auth::id())
            ->whereIn('estado', ['pendiente', 'pagada'])
            ->whereDate('fecha', '>=', date('Y-m-d'))
            ->exists();

        if ($tieneActiva) {
            return back()->withErrors(['error' => 'Solo puedes tener una reserva activa. Cancela la actual para crear otra.'])->withInput();
        }

        // 2) parseo de horas (más tolerante)
        try {
            $h1 = Carbon::parse($request->hora_inicio);
            $h2 = Carbon::parse($request->hora_fin);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Formato de hora inválido. Usa HH:MM.'])->withInput();
        }

        $minutes = $h2->diffInMinutes($h1);

        if ($minutes < 30) {
            return back()->withErrors(['error' => 'La duración mínima es de 30 minutos.'])->withInput();
        }
        if ($minutes > 8 * 60) {
            return back()->withErrors(['error' => 'La duración máxima es de 8 horas.'])->withInput();
        }

        // 3) comprobar solapamiento con otras reservas de la misma consola (misma fecha)
        $overlap = Reserva::where('consola_id', $request->consola_id)
            ->where('fecha', $request->fecha)
            ->where(function($q) use ($request) {
                $s = $request->hora_inicio;
                $e = $request->hora_fin;
                $q->where(function($q2) use ($s, $e) {
                    $q2->where('hora_inicio', '<', $e)->where('hora_fin', '>', $s);
                });
            })->exists();

        if ($overlap) {
            return back()->withErrors(['error' => 'La consola no está disponible en ese horario.'])->withInput();
        }

        // 4) Calcular precio en servidor
        $precioMap = [
            'PS4' => 3500,
            'PS5' => 5000,
            'Xbox One' => 3500,
            'Xbox Series X' => 4000,
            'Xbox 360' => 3000,
        ];
        $consola = Consola::findOrFail($request->consola_id);
        $precio_hora = $precioMap[$consola->nombre] ?? array_values($precioMap)[0];
        $horas = $minutes / 60;
        $precio = $precio_hora * $horas;

        // 5) crear reserva
        $reserva = Reserva::create([
            'user_id' => Auth::id(),
            'consola_id' => $request->consola_id,
            'fecha' => $request->fecha,
            'hora_inicio' => $request->hora_inicio,
            'hora_fin' => $request->hora_fin,
            'precio' => $precio,
            'estado' => 'pendiente'
        ]);

        // 6) si no hay token de MercadoPago: simulación
        if (!env('MERCADOPAGO_ACCESS_TOKEN')) {
            return redirect()->route('reservas.simulate', $reserva->id);    
        }

        return redirect()->route('reservas.mis')->with('success','Reserva creada (pendiente de pago).');
    }

    // Mis reservas: devuelve UNA reserva activa (si existe) + historial pasado
    public function misReservas()
    {
        $reservaActiva = Reserva::with('consola')
            ->where('user_id', Auth::id())
            ->whereIn('estado', ['pendiente', 'pagada'])
            ->whereDate('fecha', '>=', date('Y-m-d'))
            ->orderBy('fecha','asc')
            ->first();

        $historial = Reserva::with('consola')
            ->where('user_id', Auth::id())
            ->whereDate('fecha', '<', date('Y-m-d'))
            ->orderBy('fecha','desc')
            ->get();

        return view('reservas.mis', compact('reservaActiva','historial'));
    }

    // Index: ver todas las reservas (ordenadas por fecha y hora)
    public function index()
    {
        $reservas = Reserva::with('consola','user')
            ->orderBy('fecha','asc')
            ->orderBy('hora_inicio','asc')
            ->get();

        return view('reservas.index', compact('reservas'));
    }

    // Cancelar: validar permisos y tiempo antes con parse tolerante
    public function cancelar($id)
    {
        $reserva = Reserva::findOrFail($id);

        // permiso
        if ($reserva->user_id != Auth::id() && Auth::user()->role !== 'admin') {
            abort(403);
        }

        // parse flexible para evitar "Trailing data"
        try {
            $start = Carbon::parse($reserva->fecha . ' ' . $reserva->hora_inicio);
        } catch (\Exception $e) {
            if (Auth::user()->role === 'admin') {
                $reserva->estado = 'cancelada';
                $reserva->cancel_reason = 'Cancelado por admin (formato hora inválido)';
                $reserva->cancelled_by = Auth::id();
                $reserva->save();
                return back()->with('success','Reserva cancelada por admin (formato hora irregular).');
            }
            return back()->withErrors(['error' => 'No se pudo procesar la hora de la reserva (formato inválido). Contacta al admin.']);
        }

        $minutesUntil = Carbon::now()->diffInMinutes($start, false);

        if ($reserva->user_id == Auth::id() && $minutesUntil < 60) {
            return back()->withErrors(['error' => 'No se puede cancelar si falta menos de 1 hora']);
        }

        $reserva->estado = 'cancelada';
        $reserva->cancel_reason = ($reserva->user_id == Auth::id()) ? 'Cancelado por usuario' : 'Cancelado por admin';
        $reserva->cancelled_by = Auth::id();
        $reserva->save();

        return back()->with('success','Reserva cancelada.');
    }

    // Destroy (soft cancel) por admin
    public function destroy($id)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $reserva = Reserva::findOrFail($id);
        $reserva->estado = 'cancelada';
        $reserva->cancel_reason = 'Cancelado por admin';
        $reserva->cancelled_by = Auth::id();
        $reserva->save();
        return back()->with('success','Reserva eliminada por admin.');
    }

    // Simulación de pago - vista simple
    public function simulatePayment($id)
    {
        $reserva = Reserva::with('consola','user')->findOrFail($id);
        if ($reserva->user_id !== Auth::id() && Auth::user()->role !== 'admin') abort(403);
        return view('reservas.simulate', compact('reserva'));
    }

    // Confirmar simulación
    public function simulateConfirm(Request $request, $id)
    {
        $reserva = Reserva::findOrFail($id);
        if ($reserva->user_id !== Auth::id() && Auth::user()->role !== 'admin') abort(403);

        $reserva->estado = 'pagada';
        $reserva->pago_id = 'SIM-'.time();
        $reserva->save();

        return redirect()->route('reservas.mis')->with('success','Pago simulado y reserva confirmada.');
    }
}
