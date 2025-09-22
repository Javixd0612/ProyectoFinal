<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactoController extends Controller
{
    public function enviar(Request $request)
    {
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email' => 'required|email',
            'mensaje' => 'required|string|max:1000',
        ]);

        $data = $request->all();

        Mail::send('emails.contacto', $data, function ($message) use ($data) {
            $message->to('tecnojuegosempresagaming@gmail.com') // cambia al correo donde quieres recibir
                    ->subject('Nuevo mensaje de contacto');
        });

        return back()->with('success', 'Tu mensaje fue enviado con éxito.');
    }
}
