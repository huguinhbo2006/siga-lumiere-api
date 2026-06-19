<?php

namespace App\Http\Controllers;

use App\Webvideoconfiguracione;
use App\Webpagina;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConfiguracionvideoController extends Controller
{
    function nuevo(Request $request){
        try {
            $final = str_replace("https://www.youtube.com/watch?v=", "", $request['video']);
            $final = explode("&", $final);
            $existe = Webvideoconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            if(count($existe) > 0){
                $video = $existe[0];
                $video->texto = $request['texto'];
                $video->idPagina = $request['idPagina'];
                $video->video = $final[0];
                $video->save();
                return response()->json($video, 200);
            }else{
                $video = Webvideoconfiguracione::create([
                    'idConfiguracion' => $request['idConfiguracion'],
                    'texto' => $request['texto'],
                    'video' => $final[0],
                    'idPagina' => $request['idPagina'],
                    'activo' => 1,
                    'eliminado' => 0
                ]);
                return response()->json($video, 200);
            }
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $video = Webvideoconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            $respuesta = array(
            	'datos' => (count($video) > 0) ? $video[0] : null,
            	'lista' => Webpagina::where('eliminado', '=', 0)->where('activo', '=', 1)->get() 
            );
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}