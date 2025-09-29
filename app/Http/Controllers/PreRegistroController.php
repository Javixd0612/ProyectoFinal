<?php

namespace App\Http\Controllers;

use App\Models\PreRegistro;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class PreRegistroController extends Controller
{
    public function showRegisterForm()
    {
        return view('auth.register'); // usa la vista normal de registro
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:pre_registros|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $token = Str::random(64);

        $pre = PreRegistro::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'token' => $token,
        ]);

        // Enviar correo con link de confirmación
        Mail::send('emails.verify-pre', ['token' => $token], function($message) use($request){
            $message->to($request->email);
            $message->subject('Confirma tu correo');
        });

        // 👉 Se queda en la página de registro con un mensaje
        return back()->with('status', '📩 Te enviamos un enlace de verificación a tu correo. Revisa tu bandeja de entrada.');
    }

    public function verify($token)
    {
        $pre = PreRegistro::where('token', $token)->firstOrFail();

        $user = User::create([
            'name' => $pre->name,
            'email' => $pre->email,
            'password' => $pre->password,
        ]);

        $pre->delete();

        Auth::login($user);

        // 👉 Redirige al dashboard ya logueado
        return redirect('/dashboard')->with('success', '✅ Tu cuenta fue confirmada correctamente.');
    }
}
