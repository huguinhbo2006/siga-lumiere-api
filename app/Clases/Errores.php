<?php

namespace App\Clases;

use Throwable;
use Illuminate\Http\Request;

class Errores
{
    public static function enviar(Throwable $exception, Request $request)
    {
        $token = env('TELEGRAM_BOT_TOKEN');
        $chat_id = env('TELEGRAM_CHAT_ID');

        $url = "https://api.telegram.org/bot{$token}/sendMessage";

        $mensaje = "🚨 *Error en API Lumiere SIGA*\n\n";

        $mensaje .= "*Mensaje:* " . $exception->getMessage() . "\n";
        $mensaje .= "*Archivo:* " . $exception->getFile() . "\n";
        $mensaje .= "*Linea:* " . $exception->getLine() . "\n\n";

        $mensaje .= "*URL:* " . $request->fullUrl() . "\n";
        $mensaje .= "*Metodo:* " . $request->method() . "\n";
        $mensaje .= "*IP:* " . $request->ip() . "\n";

        $data = [
            'chat_id' => $chat_id,
            'text' => $mensaje,
            'parse_mode' => 'Markdown'
        ];

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_exec($ch);

        curl_close($ch);
    }
}