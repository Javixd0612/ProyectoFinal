<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;
use Carbon\Carbon;

class ReservaApiController extends Controller
{
    public function events(Request $request)
    {
        $reservas = Reserva::with('consola')->whereIn('estado',['pendiente','pagada'])->get();

        $events = $reservas->map(function($r){
            $start = Carbon::createFromFormat('Y-m-d H:i', $r->fecha.' '.$r->hora_inicio);
            $end = Carbon::createFromFormat('Y-m-d H:i', $r->fecha.' '.$r->hora_fin);
            return [
                'id' => $r->id,
                'title' => "{$r->consola->nombre} - {$r->estado}",
                'start' => $start->toIso8601String(),
                'end' => $end->toIso8601String(),
                'extendedProps' => [
                    'consola' => $r->consola->nombre ?? 'Consola',
                    'estado' => $r->estado
                ],
            ];
        });
        return response()->json($events);
    }
}
