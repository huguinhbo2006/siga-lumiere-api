<?php

namespace App\Exceptions;

use Throwable;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use App\Clases\Errores;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        $debug = env('APP_DEBUG', false);

        $status = 500;
        $message = 'Error interno del servidor';
        $errors = null;

        // 🔹 Modelo no encontrado (findOrFail)
        if ($exception instanceof ModelNotFoundException) {
            $status = 404;
            $message = 'Recurso no encontrado';
        }

        // 🔹 Error de validación
        elseif ($exception instanceof ValidationException) {
            $status = 422;
            $message = 'Error de validación';
            $errors = $exception->errors();
        }

        // 🔹 Error HTTP
        elseif ($exception instanceof HttpException) {
            $status = $exception->getStatusCode();
            $message = $exception->getMessage() ?: 'Error HTTP';
        }

        // 🚨 Enviar solo errores críticos al bot (evita spam)
        if (
            $status >= 500 &&
            !($exception instanceof ValidationException)
        ) {
            try {
                Errores::enviar($exception, $request);
            } catch (\Throwable $e) {
                // Evitar que falle el bot afecte la respuesta
            }
        }

        // 🔹 Construir respuesta JSON limpia
        $response = response()->json([
            'success' => false,
            'message' => $message,
            'errors'  => $errors,
            'error'   => $debug ? $exception->getMessage() : null,
            'file'    => $debug ? $exception->getFile() : null,
            'line'    => $debug ? $exception->getLine() : null,
        ], $status);

        // 🔥 FORZAR CORS EN TODAS LAS RESPUESTAS
        $response->headers->set('Access-Control-Allow-Origin', '*');
        $response->headers->set('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');
        $response->headers->set(
            'Access-Control-Allow-Headers',
            'Content-Type, Authorization, X-Requested-With, X-Usuario, X-UsuarioID, X-SucursalID, X-CalendarioID, X-SemanaID'
        );
        $response->headers->set('Access-Control-Allow-Credentials', 'true');

        return $response;
    }
}
