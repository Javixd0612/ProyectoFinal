<?php
namespace App\Mail;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Reserva;

class ReservaCancelada extends Mailable
{
    use Queueable, SerializesModels;
    public $reserva;
    public function __construct(Reserva $reserva){ $this->reserva = $reserva; }
    public function build()
    {
        return $this->subject('Reserva cancelada')->markdown('emails.reserva_cancelada');
    }
}
