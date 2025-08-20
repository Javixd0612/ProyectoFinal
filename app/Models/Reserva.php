<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
    'user_id', 'consola_id', 'fecha', 'hora_inicio', 'hora_fin',
    'precio', 'estado', 'pago_id', 'cancel_reason', 'cancelled_by'
];

    public function consola()
    {
        return $this->belongsTo(Consola::class);
    }

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }
}
