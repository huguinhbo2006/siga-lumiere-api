<?php  

  namespace App\Clases;
  use App\Clases\Fechas;
  use App\Clases\Empleados;
  use App\Nivele;
  use App\Usuario;
  use App\Calendario;
  use App\Comisioncurso;
  use App\Curso;
  use Illuminate\Support\Facades\DB;

  class Comisiones{

  	function comisiones($mes, $year, $usuarioID){
  		try {
  			$consulta = "SELECT f.fecha, f.idNivel, c.nombre AS calendario, n.nombre AS nivel, m.nombre AS modalida, cu.nombre AS curso, 
			s.nombre AS plantelImparticion, ca.nombre AS categoria, CONCAT(al.apellidoPaterno, ' ', al.apellidoMaterno) AS apellidos,
			al.nombre AS nombres, ac.precio AS precio,
			(SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id AND eliminado = 0) AS descuentos,
			(SELECT SUM(cantidad) FROM alumnodescuentos WHERE idFicha = f.id AND tipo = 1 AND eliminado = 0) AS cantidad,
			((ac.precio) - (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id AND eliminado = 0)) AS final,
			(SELECT SUM(monto) FROM alumnoextras WHERE idFicha = f.id AND eliminado = 0) as extras,
			(SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = f.id AND eliminado = 0) abonos,
			(((ac.precio) - (SELECT SUM(monto) FROM alumnodescuentos WHERE idFicha = f.id AND eliminado = 0)) - (SELECT SUM(monto) FROM alumnoabonos WHERE idFicha = f.id AND eliminado = 0)) AS saldo, cc.comision as porcentaje, cc.tipo as tipo
			FROM fichas f, grupos g, altacursos ac, calendarios c, niveles n, modalidades m, cursos cu, sucursales s, categorias ca, alumnos al, comisioncursos cc 
			WHERE f.idGrupo = g.id AND ac.id = g.idAltaCurso AND c.id = ac.idCalendario AND n.id = ac.idNivel 
			AND m.id = ac.idModalidad AND cu.id = ac.idCurso AND f.idSucursalImparticion = s.id AND ca.id = ac.idCategoria
			AND al.id = f.idAlumno
			AND f.idUsuarioInformacion = $usuarioID AND MONTH(fecha) = $mes AND YEAR(fecha) = $year AND ac.idNivel <> 2 AND cc.idCalendario = ac.idCalendario AND cc.idCurso = ac.idCurso";

			return DB::select($consulta, array());	
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function listas(){
  		try {
  			$fechas = new Fechas();
  			$empleados = new Empleados();
  			return array(
  				'years' => $fechas->years(2021),
  				'empleados' => Usuario::join('empleados', 'idEmpleado', '=', 'empleados.id')->
                                  select('usuarios.id as id', 'empleados.nombre as nombre')->
                                  where('empleados.idDepartamento', '=', 6)->get(),
  				'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
                'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->whereRaw('fin > NOW()')->get(),
                'cursos' => Curso::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
  			);
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function calcular($fichas){
  		try {
  			foreach ($fichas as $ficha) {
                $descuento = (is_null($ficha->descuentos)) ? 0 : floatval($ficha->descuentos);
                $extras = (is_null($ficha->extras)) ? 0 : floatval($ficha->extras);
                $ficha->final = floatval($ficha->precio) - $descuento + $extras;

                $ficha->comision = (intval($ficha->tipo) === 1) ? 
                    round(floatval($ficha->final) * floatval(floatval($ficha->porcentaje)/100), 2) : 
                    round(floatval($ficha->porcentaje), 2);
                $ficha->porcentaje = ($ficha->tipo === 1) ? $ficha->porcentaje : '-';

            }
            return $fichas;
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function comisionTotal($fichas){
  		try {
  			$comisionTotal = 0;
  			foreach ($fichas as $ficha) {
                $comisionTotal = $comisionTotal + floatval($ficha->comision);
            }
            return $comisionTotal;
  		} catch (Exception $e) {
  			return null;
  		}
  	}

    function comisionesActuales(){
        try {
            return Comisioncurso::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('cursos', 'idCurso', '=', 'cursos.id')->
            select(
                'comisioncursos.*',
                'calendarios.nombre as calendario',
                'cursos.nombre as curso',
                'calendarios.id as idCalendario',
                DB::raw(
                    "IF(comisioncursos.tipo = 1, 
                        CONCAT(comisioncursos.comision, '%'),
                        CONCAT('$', comisioncursos.comision)
                    ) as total"
                )
            )->
            where('comisioncursos.eliminado', '=', 0)->
            whereRaw('calendarios.fin > NOW()')->get();
        } catch (Exception $e) {
            return null;
        }
    }

    function existeComisionCurso($calendarioID, $cursoID){
        try {
            return (Comisioncurso::where('idCalendario', '=', $calendarioID)->
                        where('idCurso', '=', $cursoID)->count() > 0);
        } catch (Exception $e) {
            return false;
        }
    }

    function nuevaComisionCurso($calendarioID, $cursoID, $tipo, $comision){
        try {
            return Comisioncurso::create([
                'idCalendario' => $calendarioID,
                'idCurso' => $cursoID,
                'tipo' => $tipo,
                'comision' => $comision,
                'eliminado' => 0,
                'activo' => 1
            ]);
        } catch (Exception $e) {
            return null;
        }
    }

    function formatearComisionCurso($comision){
        try {
            $comision->calendario = Calendario::find($comision->idCalendario)->nombre;
            $comision->curso = Curso::find($comision->idCurso)->nombre;
            $comision->total = (intval($comision->tipo) === 1) ? $comision->comision.'%' : '$'.$comision->comision;
            return $comision;
        } catch (Exception $e) {
            return null;
        }
    }

    function modificarComisionCurso($id, $tipo, $comision){
        try {
            $dato = Comisioncurso::find($id);
            $dato->tipo = $tipo;
            $dato->comision = $comision;
            $dato->save();
            return $dato;
        } catch (Exception $e) {
            return null;
        }
    }

    function eliminarComisionCurso($id){
        try {
            $dato = Comisioncurso::find($id);
            $dato->eliminado = 1;
            $dato->save();
            return $dato;
        } catch (Exception $e) {
            return null;
        }
    }
  }

?>