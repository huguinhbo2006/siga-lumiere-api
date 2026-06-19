<?php

namespace App\Http\Controllers;
use App\Curso;
use App\Altacurso;
use Carbon\Carbon;

use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;

class CursosController extends BaseController
{
    function nuevo(Request $request){
        try{
            $curso = Curso::create([
                'nombre' => $request['nombre'],
                'icono' => $request['icono'],
                'eliminado' => 0,
                'activo' => 1
            ]);
            

            return response()->json($curso, 200);
        }catch(Exception $e){
            return response()->json('Error al crear curso', 400);
        }
    }

    function mostrar(){
        try{
            $cursos = Curso::where('eliminado', '=', 0)->get();
            return response()->json($cursos, 200);
        }catch(Exception $e){
            return response()->json('Error al mostrar cursos', 400);
        }
    }

    function activar(Request $request){
        try{
            $curso = Curso::find($request['id']);
            $curso->activo = 1;
            $curso->save();

            return response()->json($curso, 200);
        }catch(Exception $e){
            return response()->json('Error al activar curso', 400);
        }
    }

    function desactivar(Request $request){
        try{
            $curso = Curso::find($request['id']);
            $curso->activo = 0;
            $curso->save();

            return response()->json($curso, 200);
        }catch(Exception $e){
            return response()->json('Error al desactivar curso', 400);
        }
    }

    function eliminar(Request $request){
        try{
            $curso = Curso::find($request['id']);
            $curso->eliminado = 1;
            $curso->save();

            return response()->json($curso, 200);
        }catch(Exception $e){
            return response()->json('Error al eliminar curso', 400);
        }
    }

    function modificar(Request $request){
        try{
            $curso = Curso::find($request['id']);
            $curso->nombre = $request['nombre'];
            $curso->icono = $request['icono'];
            $curso->save();

            return response()->json($curso, 200);
        }catch(Exception $e){
            return response()->json('Error al modificar curso', 400);
        }
    }

    function udg(){
        try{
            $hoy = Carbon::now();
            $cursos = Curso::join('altacursos', 'altacursos.idCurso', '=', 'cursos.id')
                ->join('calendarios', 'calendarios.id', '=', 'altacursos.idCalendario')
                ->where('altacursos.idNivel', 1)
                ->where('altacursos.activo', 1)
                ->where('altacursos.eliminado', 0)
                ->where('cursos.activo', 1)
                ->where('cursos.eliminado', 0)
                ->where('calendarios.activo', 1)
                ->where('calendarios.eliminado', 0)
                ->whereDate('calendarios.inicio', '<=', $hoy)
                ->whereDate('calendarios.fin', '>=', $hoy)
                ->select('cursos.*')
                ->distinct()
                ->orderBy('cursos.id', 'asc')
                ->get();
            return response()->json($cursos, 200);
        }catch(Exception $e){
            return response()->json('Error al mostrar cursos', 400);
        }
    }
}