<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reserva;

class MercadoPagoController extends Controller
{
    public function webhook(Request $request)
    {
        $body = $request->all();
        $payment_id = $body['data']['id'] ?? null;
        $type = $body['type'] ?? null;

        if ($type === 'payment' && $payment_id) {
            \MercadoPago\SDK::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
            try {
                $payment = \MercadoPago\Payment::find_by_id($payment_id);
                $external_reference = $payment->external_reference ?? null;
                if ($external_reference && strpos($external_reference,'reserva_')===0) {
                    $reservaId = intval(str_replace('reserva_','',$external_reference));
                    $reserva = Reserva::find($reservaId);
                    if ($reserva && $payment->status === 'approved') {
                        $reserva->estado = 'pagada';
                        $reserva->pago_id = $payment_id;
                        $reserva->save();
                    }
                }
            } catch (\Exception $e) {
                \Log::error('MP webhook error: '.$e->getMessage());
            }
        }
        return response()->json(['ok'=>true]);
    }
}
