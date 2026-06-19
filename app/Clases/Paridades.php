<?php  

  namespace App\Clases;
  use Illuminate\Support\Facades\DB;
  use App\Paridade;
  use App\Cursosparidade;
  use App\Clases\Consultas;

  class Paridades{

  	function cupos($paridadID, $calendarioID, $sucursalID){
  		try {

  			return Paridade::join('cursosparidades', 'cursosparidades.idParidad', '=', 'paridades.id')->
  			join('cursos', 'cursosparidades.idCurso', '=', 'cursos.id')->
  			join('altacursos', 'cursos.id', '=', 'altacursos.idCurso')->
  			join('grupos', 'altacursos.id', '=', 'grupos.idAltacurso')->
  			join('horarios', 'grupos.idHorario', '=', 'horarios.id')->
  			join('reservacionesaulas', 'reservacionesaulas.idGrupo', '=', 'grupos.id')->
  			join('aulas', 'reservacionesaulas.idAula', '=', 'aulas.id')->
  			select(
  				DB::raw("CONCAT(horarios.inicio, ' - ', horarios.fin) as horario"),
  				'horarios.id',
  				'grupos.id as idGrupo',
  				DB::raw("(SELECT COUNT(*) FROM fichas where idSucursalImparticion = ". $sucursalID ." AND idGrupo = grupos.id) as inscritos"),
  				'cursos.icono',
  				'aulas.cupo'
  			)->where('paridades.id', '=', $paridadID)->
  			where('altacursos.idCalendario', '=', $calendarioID)->
  			where('reservacionesaulas.idSucursal', '=', $sucursalID)->
  			groupBy('horarios.id', 'horarios.inicio', 'horarios.fin', 'grupos.id', 'aulas.cupo', 'cursos.icono')->get();
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function horarios($paridadID, $calendarioID){
  		try {
  			return Paridade::join('cursosparidades', 'cursosparidades.idParidad', '=', 'paridades.id')->
  			join('cursos', 'cursosparidades.idCurso', '=', 'cursos.id')->
  			join('altacursos', 'cursos.id', '=', 'altacursos.idCurso')->
  			join('grupos', 'altacursos.id', '=', 'grupos.idAltacurso')->
  			join('horarios', 'grupos.idHorario', '=', 'horarios.id')->
  			select(
  				DB::raw("CONCAT(horarios.inicio, ' - ', horarios.fin) as horario"),
  				'horarios.id',
  			)->where('paridades.id', '=', $paridadID)->
  			where('altacursos.idCalendario', '=', $calendarioID)->
  			groupBy('horarios.id', 'horarios.inicio', 'horarios.fin')->get();
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function control($horarios, $cupos){
  		try {
  			foreach ($horarios as $horario) {
  				$inscritos = 0;
  				$profesores = array();
  				foreach ($cupos as $cupo) {
  					if(intval($horario->id) === intval($cupo->id)){
  						$profesor = array();
  						$horario->cupo = $cupo->cupo;
  						$inscritos = $inscritos + intval($cupo->inscritos);
  						$profesor['nombre'] = $cupo->inscritos;
  						$profesor['imagen'] = $cupo->icono;
  						$profesores[] = $profesor;
  					}
  				}
  				$horario->profesores = $profesores;
  				$horario->inscritos = $inscritos;
  				$horario->lugares = intval($horario->cupo) - intval($inscritos);
  			}
  			return $horarios;
  		} catch (Exception $e) {
  			return null;
  		}
  	}

    function grupos($paridadID){
      try {
        return Paridade::join('cursosparidades', 'cursosparidades.idParidad', '=', 'paridades.id')->
        join('cursos', 'cursosparidades.idCurso', '=', 'cursos.id')->
        join('altacursos', 'cursos.id', '=', 'altacursos.idCurso')->
        join('grupos', 'altacursos.id', '=', 'grupos.idAltacurso')->
        select(
          'grupos.id'
        )->where('paridades.id', '=', $paridadID)->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function cursos($paridadID){
      try {
        return Cursosparidade::join('cursos', 'idCurso', '=', 'cursos.id')->
        select('cursos.id')->
        where('idParidad', '=', $paridadID)->get();
      } catch (Exception $e) {
        return null;
      }
    }
  }

?>