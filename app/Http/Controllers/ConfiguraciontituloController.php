<?php

namespace App\Http\Controllers;

use App\Webtituloconfiguracione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConfiguraciontituloController extends Controller
{
    function nuevo(Request $request){
        try {
            $existe = Webtituloconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            if(count($existe) > 0){
                $titulo = $existe[0];
                $titulo->texto = $request['texto'];
                $titulo->clase = $request['clase'];
                $titulo->save();
                return response()->json($titulo, 200);
            }else{
                $titulo = Webtituloconfiguracione::create([
                    'idConfiguracion' => $request['idConfiguracion'],
                    'texto' => $request['texto'],
                    'clase' => $request['clase'],
                    'activo' => 1,
                    'eliminado' => 0
                ]);
                return response()->json($titulo, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $titulo = Webtituloconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            return response()->json($titulo[0], 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}