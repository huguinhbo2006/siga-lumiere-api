<?php

namespace App\Http\Controllers;
use App\Clases\Metascategorias;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MetascategoriasController extends BaseController
{
    function mostrar(){
        try {
            $funciones = new Metascategorias();
            return response()->json(array(
                'datos' => $funciones->mostrar(),
                'listas' => $funciones->listas() 
            ), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $funciones = new Metascategorias();
            if($funciones->existe($request['idCalendario'], $request['idSucursal'], $request['idCategoria'])){
                return response()->json('Ya existe una meta para el calendario, sucursal y categoria seleccionadas', 400);
            }
            $meta = $funciones->nuevo($request['idCalendario'], $request['idSucursal'], $request['idCategoria'], $request['meta']);
            return response()->json($funciones->completar($meta->id), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $funciones = new Metascategorias();
            $meta = $funciones->modificar($request['id'], $request['meta']);
            return response()->json($funciones->completar($meta->id), 200);
        } catch (Exception $e) {
            
        }
    }

    function eliminar(Request $request){
        try {
            $funciones = new Metascategorias();
            return response()->json($funciones->eliminar($request['id']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}