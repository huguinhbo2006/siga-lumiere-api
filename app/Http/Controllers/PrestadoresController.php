<?php

namespace App\Http\Controllers;

use App\Prestador;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class PrestadoresController extends Controller
{
    function mostrar(){
        try {
            $datos = Prestador::where('eliminado', '=', 0)->get();
            return response()->json($datos, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $data = $request->only(['nombre']);
            $dato = Prestador::create($data);
            return response()->json($dato, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function modificar(Request $request){
        try {
            $dato = Prestador::findOrFail($request->input('id'));
            $data = $request->only([]);

            $dato->update($data);

            return response()->json($dato, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function activar(Request $request){
        try {
            $dato = Prestador::find($request['id']);
            $dato->activo = 1;
            $dato->save();
            return response()->json($dato, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function desactivar(Request $request){
        try {
            $dato = Prestador::find($request['id']);
            $dato->activo = 0;
            $dato->save();
            return response()->json($dato, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $dato = Prestador::find($request['id']);
            $dato->eliminar = 1;
            $dato->save();
            return response()->json($dato, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}