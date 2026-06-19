<?php

namespace App\Http\Controllers;

use App\Webaltasconfiguracione;
use App\Calendario;
use App\Categoria;
use App\Curso;
use App\Altacurso;
use App\Clases\Altacursos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ConfiguracioncursosController extends Controller
{
    function mostrar(Request $request){
        try {
        	$altas = new Altacursos();
            $configuracion = $request['idConfiguracion'];
            $consulta = "SELECT n.nombre as nivel, s.nombre as subnivel, m.nombre as modalidad, ca.nombre as calendario, c.nombre as curso, i.*, 
                        cat.nombre as categoria, se.nombre as sede, c.icono as icono 
                        FROM niveles n, subniveles s, modalidades m, calendarios ca, cursos c, altacursos i,
                        categorias cat, sedes se, webaltasconfiguraciones w 
                        WHERE i.eliminado = 0 AND i.idModalidad = m.id AND i.idNivel = n.id AND i.idSubnivel = s.id AND i.idCalendario = ca.id 
                        AND i.idCurso = c.id AND i.idCategoria = cat.id AND i.idSede = se.id AND w.idAltaCurso = i.id AND w.idConfiguracion = $configuracion";
            $datos = DB::select($consulta, array());
            $respuesta = array(
            	'datos' => $datos,
            	'listas' => array(
            		'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->whereRaw('fin > NOW()')->get(),
            		'categorias' => Categoria::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
            		'cursos' => Curso::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
            		'altas' => $altas->traerCursos()
            	) 
            );
            return response()->json($respuesta, 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function nuevo(Request $request){
        try {
            $lista = $request['lista'];
            $configuracion = $request['idConfiguracion'];
            DB::table('webaltasconfiguraciones')->where('idConfiguracion', '=', $configuracion)->delete();
            $posicion = 1;
            foreach ($lista as $registro) {
                $curso = Webaltasconfiguracione::create([
                    'idConfiguracion' => $configuracion,
                    'idAltaCurso' => $registro['id'],
                    'posicion' => $posicion,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
                $posicion++;
            }
            return response()->json('Cursos cargados correctamente', 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }
}