<?php

namespace App\Clases;

use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class Listas
{
    public static function listas(array $tablas)
    {
        try {
            $listas = [];
            $hoy = Carbon::today();

            foreach ($tablas as $tabla) {

                if ($tabla == 'actuales') {
                    $listas[$tabla] = DB::table('calendarios')
                        ->where('eliminado', 0)
                        ->where('activo', 1)
                        ->whereDate('fin', '>=', $hoy)
                        ->get();

                    continue;
                }

                if ($tabla == 'antiguos') {
                    $listas[$tabla] = DB::table('calendarios')
                        ->where('eliminado', 0)
                        ->where('activo', 1)
                        ->whereDate('inicio', '>', $hoy->copy()->subYear())
                        ->get();

                    continue;
                }

                // Validamos que la tabla exista antes de continuar
                if (!DB::getSchemaBuilder()->hasTable($tabla)) {
                    continue;
                }

                // Lógica especial para la tabla 'horarios'
                if ($tabla == 'horarios') {
                    $listas[$tabla] = DB::table('horarios')
                        ->where('eliminado', 0)
                        ->where('activo', 1)
                        ->get()
                        ->map(function ($item) {
                            // Creamos el campo 'nombre' uniendo inicio y fin
                            $item->nombre = $item->inicio . ' - ' . $item->fin;
                            return $item;
                        });

                    continue;
                }

                // Comportamiento por defecto para el resto de las tablas
                $listas[$tabla] = DB::table($tabla)
                    ->where('eliminado', 0)
                    ->where('activo', 1)
                    ->get();
            }

            return $listas;

        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }
}