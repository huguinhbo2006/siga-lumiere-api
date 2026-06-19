<?php

namespace App\Http\Controllers;

use App\Usuario;
use App\Empleado;
use App\Calendario;
use App\Usuariosucursale;
use App\Sucursale;
use App\Ficha;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;

class DesarrolloController extends Controller
{
    function verPassword(Request $request){
        try{
            $codificacion = Hash::make($request['password']);
            $respuesta['codificacion'] = $codificacion;
            return response()->json($respuesta, 200);
        }catch(Exception $e){
            return response()->json("Error en el servidor", 400);
        }
    }

    function testing(Request $request){
        try {
            $idFicha = $request['idFicha'];
            $idExamen = $request['idExamen'];

            $aciertosPorPorcentaje = DB::table('examenporcentajes as ep')
            ->join('seccionesporcentajes as sp', 'sp.idPorcentaje', '=', 'ep.id')
            ->join('secciones as s', function ($join) {
                $join->on('s.id', '=', 'sp.idSeccion')
                     ->on('s.idExamen', '=', 'ep.idExamen');
            })
            ->join('calificaciones as c', function ($join) {
                $join->on('c.idSeccion', '=', 's.id')
                     ->on('c.idExamen', '=', 's.idExamen');
            })
            ->select(
                'ep.idExamen',
                'ep.id as idPorcentaje',
                'ep.nombre as porcentaje',
                DB::raw('SUM(c.aciertos) as total_aciertos')
            )
            ->where('c.idFicha', $idFicha)     // 🔸 Filtro por ficha
            ->where('ep.idExamen', $idExamen)  // 🔸 Filtro por examen
            ->groupBy('ep.idExamen', 'ep.id', 'ep.nombre')
            ->get();

            return response()->json($aciertosPorPorcentaje, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}