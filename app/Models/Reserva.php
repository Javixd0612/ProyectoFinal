<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'user_id','console_id','date','start_time','end_time','total_price','status','notes'
    ];

    public function user()
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function console()
    {
        return $this->belongsTo(\App\Models\Console::class, 'console_id');
    }

    public function isActive(): bool
    {
        return in_array($this->status, ['pending','paid']) && Carbon::parse($this->date)->isAfter(now()->subDay());
    }
}
