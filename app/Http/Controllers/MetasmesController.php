<?php

namespace App\Http\Controllers;
use App\Clases\Metasmes;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetasmesController extends BaseController
{
    function nuevo(Request $request){
        try {
            $funciones = new Metasmes();
            if($funciones->existe($request['idCalendario'], $request['idNivel'], $request['idSubnivel'], $request['idSucursal'], $request['mes'])){
                return response()->json('Ya existe una meta para el calendario, nivel, subnivel, sucursal y mes seleccionados');
            }
            $meta = $funciones->nuevo($request['idCalendario'], $request['idNivel'], $request['idSubnivel'], $request['idSucursal'], $request['mes'], $request['meta']);
            return response()->json($funciones->completar($meta->id), 200);
        } catch (Exception $e) {
            
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Metasmes();
            $meta = $funciones->modificar($request['id'], $request['meta']);
            return response()->json($funciones->completar($meta->id), 200);
        } catch (Exception $e) {
            
        }
    }

    function mostrar(Request $request){
        try {
            $funciones = new Metasmes();
            return response()->json(array(
                'datos' => $funciones->mostrar(),
                'listas' => $funciones->listas() 
            ), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $funciones = new Metasmes();
            return response()->json($funciones->eliminar($request['id']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}