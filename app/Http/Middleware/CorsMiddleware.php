<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CorsMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $origin = $request->headers->get('Origin');
        $allowedOrigins = [
            'https://nueva.lumieresiga.com',
            'https://prueba.lumieresiga.com',
            'https://www.lumieresocial.com',
            'https://admin.lumieresocial.com',
            'https://dashboard.cursoslumiere.com',
            'https://nueva.cursoslumiere.com',
            'http://localhost:4200',
        ];

        $headers = [
            'Access-Control-Allow-Methods'     => 'GET, POST, PUT, DELETE, OPTIONS',
            'Access-Control-Allow-Headers'     => 'Content-Type, Authorization, X-Requested-With, X-Usuario, X-UsuarioID, X-SucursalID, X-CalendarioID, X-SemanaID',
            'Access-Control-Allow-Credentials' => 'true',
        ];

        // Verificamos si el origen está permitido
        if (in_array($origin, $allowedOrigins)) {
            $headers['Access-Control-Allow-Origin'] = $origin;
        }

        // Manejo de Pre-flight (Petición OPTIONS)
        if ($request->isMethod('OPTIONS')) {
            return response()->json('OK', 204, $headers);
        }

        $response = $next($request);

        // Aplicar cabeceras a la respuesta final de forma segura
        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        return $response;
    }
}