<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CustomResetPassword extends Notification
{
    use Queueable;

    public $token;

    public function __construct($token)
    {
        $this->token = $token;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = url(route('password.reset', [
            'token' => $this->token,
            'email' => $notifiable->getEmailForPasswordReset(),
        ], false));

        return (new MailMessage)
            ->subject('🔑 Restablece tu contraseña - TecnoJuegos')
            ->greeting('¡Hola gamer! 🎮')
            ->line('Recibimos una solicitud para restablecer tu contraseña en **TecnoJuegos**.')
            ->line('Haz clic en el siguiente botón para continuar:')
            ->action('Restablecer Contraseña', $url)
            ->line('⚡ Este enlace solo es válido por 60 minutos.')
            ->line('Si no solicitaste este cambio, simplemente ignora este mensaje.');
    }
}
