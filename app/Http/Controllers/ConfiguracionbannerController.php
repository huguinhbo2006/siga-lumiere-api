<?php

namespace App\Http\Controllers;

use App\Webbannerconfiguracione;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConfiguracionbannerController extends Controller
{
    function nuevo(Request $request){
        try {
            $totalBanners = Webbannerconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->get();
            $banner = Webbannerconfiguracione::create([
                'idConfiguracion' => $request['idConfiguracion'],
                'imagen' => $request['banner'],
                'posicion' => count($totalBanners)+1,
                'activo' => 1,
                'eliminado' =>0
            ]);
            return response()->json($banner, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function mostrar(Request $request){
        try {
            $banners = Webbannerconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->orderBy('posicion', 'ASC')->get();
            return response()->json($banners, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function actualizar(Request $request){
        try {
            $banners = $request['banners'];
            for ($i=0; $i < count($banners); $i++) { 
                $banner = Webbannerconfiguracione::find($banners[$i]['id']);
                $banner->posicion = ($i+1);
                $banner->save();
            }
            return response()->json('Todo correcto', 200);   
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function eliminar(Request $request){
        try {
            $banner = Webbannerconfiguracione::find($request['id']);
            $banner->delete();
            $banners = Webbannerconfiguracione::where('idConfiguracion', '=', $request['idConfiguracion'])->orderBy('posicion', 'ASC')->get();
            for ($i=0; $i < count($banners); $i++) { 
                $banner = Webbannerconfiguracione::find($banners[$i]['id']);
                $banner->posicion = ($i+1);
                $banner->save();
            }
            return response()->json('Todo correcto', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}