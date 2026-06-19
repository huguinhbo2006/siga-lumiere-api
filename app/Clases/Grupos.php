<?php  

  namespace App\Clases;
  use App\Grupo;
  use App\Cursosparidade;
  use Illuminate\Support\Facades\DB;

  class Grupos{

  	function actuales($sucursalID){
  		try {
  			return Grupo::join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
	        join('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
	        join('niveles', 'altacursos.idNivel', '=', 'niveles.id')->
	        join('subniveles', 'altacursos.idSubnivel', '=', 'subniveles.id')->
	        join('categorias', 'altacursos.idCategoria', '=', 'categorias.id')->
	        join('modalidades', 'altacursos.idModalidad', '=', 'modalidades.id')->
	        join('turnos', 'idTurno', '=', 'turnos.id')->
	        join('horarios', 'idHorario', '=', 'horarios.id')->
	        join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
	        join('sedes', 'altacursos.idSede', '=', 'sedes.id')->
	        leftjoin('bloqueohorarios', 'grupos.id', '=', 'bloqueohorarios.idGrupo')->
	        select(
	            'grupos.id as id',
	            'altacursos.idCalendario',
	            'altacursos.idNivel',
	            'altacursos.idSubnivel',
	            'altacursos.idCategoria',
	            'altacursos.idModalidad',
	            'altacursos.idSede',
	            'altacursos.idCurso',
	            'altacursos.inicio',
	            'altacursos.fin',
	            'altacursos.limitePago',
	            'altacursos.precio',
	            'calendarios.nombre as calendario',
	            'niveles.nombre as nivel',
	            'subniveles.nombre as subnivel',
	            'categorias.nombre as categoria',
	            'modalidades.nombre as modalidad',
	            'sedes.nombre as sede',
	            'turnos.nombre as turno',
	            DB::raw("CONCAT(horarios.inicio, ' - ', horarios.fin) as horario"),
	            'cursos.nombre as curso',
	            'cursos.icono',
	            'grupos.idHorario', 
	            'grupos.idTurno',
	            DB::raw("IF((SELECT COUNT(*) FROM bloqueohorarios WHERE idGrupo = grupos.id AND idSucursal = $sucursalID LIMIT 1) > 0, bloqueohorarios.id, 0) as idBloqueo"),
	            'bloqueohorarios.idSucursal',
	            DB::raw("IF((SELECT COUNT(*) FROM bloqueohorarios WHERE idGrupo = grupos.id AND idSucursal = $sucursalID LIMIT 1) > 0, true, false) as bloqueo"),
	        )->
	        whereRaw('NOW() BETWEEN calendarios.inicio AND calendarios.fin')->get();
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function calendario($sucursalID, $calendarioID){
  		try {
  			return Grupo::join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
	        join('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
	        join('niveles', 'altacursos.idNivel', '=', 'niveles.id')->
	        join('subniveles', 'altacursos.idSubnivel', '=', 'subniveles.id')->
	        join('categorias', 'altacursos.idCategoria', '=', 'categorias.id')->
	        join('modalidades', 'altacursos.idModalidad', '=', 'modalidades.id')->
	        join('turnos', 'idTurno', '=', 'turnos.id')->
	        join('horarios', 'idHorario', '=', 'horarios.id')->
	        join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
	        join('sedes', 'altacursos.idSede', '=', 'sedes.id')->
	        leftjoin('bloqueohorarios', 'grupos.id', '=', 'bloqueohorarios.idGrupo')->
	        select(
	            'grupos.id as id',
	            'altacursos.idCalendario',
	            'altacursos.idNivel',
	            'altacursos.idSubnivel',
	            'altacursos.idCategoria',
	            'altacursos.idModalidad',
	            'altacursos.idSede',
	            'altacursos.idCurso',
	            'altacursos.inicio',
	            'altacursos.fin',
	            'altacursos.limitePago',
	            'altacursos.precio',
	            'calendarios.nombre as calendario',
	            'niveles.nombre as nivel',
	            'subniveles.nombre as subnivel',
	            'categorias.nombre as categoria',
	            'modalidades.nombre as modalidad',
	            'sedes.nombre as sede',
	            'turnos.nombre as turno',
	            DB::raw("CONCAT(horarios.inicio, ' - ', horarios.fin) as horario"),
	            'cursos.nombre as curso',
	            'cursos.icono',
	            'grupos.idHorario', 
	            'grupos.idTurno',
	            DB::raw("IF((SELECT COUNT(*) FROM bloqueohorarios WHERE idGrupo = grupos.id AND idSucursal = $sucursalID LIMIT 1) > 0, bloqueohorarios.id, 0) as idBloqueo"),
	            'bloqueohorarios.idSucursal',
	            DB::raw("IF((SELECT COUNT(*) FROM bloqueohorarios WHERE idGrupo = grupos.id AND idSucursal = $sucursalID LIMIT 1) > 0, true, false) as bloqueo"),
	        )->
	        whereRaw('NOW() BETWEEN calendarios.inicio AND calendarios.fin')->get();
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function paridad($grupoID){
  		try {
  			$paridad = Cursosparidade::join('paridades', 'idParidad', '=', 'paridades.id')->
  			join('altacursos', 'cursosparidades.idCurso', '=', 'altacursos.idCurso')->
  			join('grupos', 'altacursos.id', '=', 'grupos.idAltacurso')->
  			select('paridades.id')->
  			where('grupos.id', '=', $grupoID)->get();
  			return (count($paridad) > 0) ? $paridad[0]->id : null;
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function grupo($nivelID, $subnivelID, $calendarioID, $modalidadID, $categoriaID, $sedeID, $turnoID, $horarioID, $cursoID){
  		try {
  			$grupo = Grupo::join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
				select('grupos.*')->
				where('altacursos.idNivel', '=', $nivelID)->
				where('altacursos.idSubnivel', '=', $subnivelID)->
				where('altacursos.idCalendario', '=', $calendarioID)->
				where('altacursos.idModalidad', '=', $modalidadID)->
				where('altacursos.idCategoria', '=', $categoriaID)->
				where('altacursos.idSede', '=', $sedeID)->
				where('grupos.idHorario', '=', $horarioID)->
				where('grupos.idTurno', '=', $turnoID)->
				where('altacursos.idCurso', '=', $cursoID)->get();
				return (count($grupo) > 0) ? $grupo[0] : null;
  		} catch (Exception $e) {
  			
  		}
  	}
  }

?>