<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Console extends Model
{
    protected $table = 'consoles';
    protected $fillable = ['name','price_per_hour'];

    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'console_id');
    }
}
