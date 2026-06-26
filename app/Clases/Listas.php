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
                        ->whereDate('inicio', '>', $hoy)
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

                if (!DB::getSchemaBuilder()->hasTable($tabla)) {
                    continue;
                }

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