<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;

class Consola extends Model
{
    protected $fillable = ['nombre','descripcion','disponible'];
    public function reservas(){ return $this->hasMany(Reserva::class); }
}
