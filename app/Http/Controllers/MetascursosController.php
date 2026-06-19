<?php

namespace App\Http\Controllers;
use App\Clases\Metascursos;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class MetascursosController extends BaseController
{
    function mostrar(){
        try {
            $funciones = new Metascursos();
            return response()->json(
                array(
                    'listas' => $funciones->listas(),
                    'datos' => $funciones->mostrar() 
                ), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $funciones = new Metascursos();
            if($funciones->existe($request['idCalendario'], $request['idNivel'], $request['idSubnivel'], $request['idModalidad'], $request['idCurso'], $request['idSucursal'])){
                return response()->json('Ya existe una meta para el calendario, nivel, subnivel, modalidad, curso y sucursal seleccionados', 400);
            }
            $meta = $funciones->nuevo($request['idCalendario'], $request['idNivel'], $request['idSubnivel'], $request['idModalidad'], $request['idCurso'], $request['idSucursal'], $request['meta']);
            return response()->json($funciones->completar($meta->id), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Metascursos();
            $meta = $funciones->modificar($request['id'], $request['meta']);
            return response()->json($funciones->completar($meta->id), 200);
        } catch (Exception $e) {
            
        }
    }

    function eliminar(Request $request){
        try {
            $funciones = new Metascursos();

            return response()->json($funciones->eliminar($request['id']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}