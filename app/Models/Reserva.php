<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reserva extends Model
{
    protected $fillable = [
        'user_id',
        'consola_id',
        'start_at',
        'end_at',
        'horas',
        'precio_total',
        'status',
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at'   => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function consola()
    {
        return $this->belongsTo(\App\Models\Consola::class);
    }

    /**
     * Comprueba si existe una reserva pagada que solape el intervalo.
     * Según la regla pedida: solo las reservas 'paid' bloquean a otras.
     */
    public static function overlaps($consolaId, $startAt, $endAt)
    {
        return self::where('consola_id', $consolaId)
            ->where('status', 'paid')
            ->where('start_at', '<', $endAt)
            ->where('end_at', '>', $startAt)
            ->exists();
    }
}
