<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Consola extends Model
{
    protected $fillable = [
        'nombre',
        'precio_hora',
        'descripcion',
    ];

    public function reservas()
    {
        return $this->hasMany(\App\Models\Reserva::class);
    }
}
