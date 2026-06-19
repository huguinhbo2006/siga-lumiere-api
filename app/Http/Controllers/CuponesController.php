<?php

namespace App\Http\Controllers;
use App\Cupone;
use App\Ficha;
use App\Alumno;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CuponesController extends BaseController
{
    function cursosCongelados(Request $request){
        try {
            $cupones = Cupone::join('fichas', 'cupones.idFicha', '=', 'fichas.id')->
            join('alumnos', 'fichas.idAlumno', '=', 'alumnos.id')->
            select([
                'fichas.id as idFicha',
                'fichas.folio as ficha',
                'alumnos.id as idAlumno',
                DB::raw("CONCAT(alumnos.nombre, ' ', alumnos.apellidoPaterno, ' ', alumnos.apellidoMaterno) as alumno"),
                'cupones.monto as monto',
                'cupones.cupon as codigo'
            ])->where('cupones.idFicha', '<>', 0)->
            where('fichas.idCalendario', '=', $request['idFicha'])->get();
            return response()->json($cupones, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function canjear(Request $request){
        try {
            $cupon = Cupone::where('cupon', '=', $request['cupon'])->first();
            if(is_null($cupon)){
                return response()->json('Cupon no encontrado', 400);
            }
            if(intval($cupon->cantidad) > 0){
                $cupon->cantidad = intval($cupon->cantidad) - 1;
                $cupon->save();
                return response()->json($cupon, 200);
            }else{
                return response()->json('El cupon ya ha sido canjeado anteriormente', 400);
            }
        } catch (Exception ) {
            return response()->json('Error en el servidor', 400);
        }
    }
}