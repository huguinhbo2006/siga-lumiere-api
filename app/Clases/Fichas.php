<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use App\Ficha;
	use App\Grupo;
	use App\Alumno;
	use App\Altacurso;
	use App\Horario;
	use App\Modalidade;
	use App\Modalidadescurso;
	use App\Curso;
	use App\Nivele;
	use App\Subnivele;
	use App\Alumnocargo;
	use App\Alumnoabono;
	use App\Alumnodescuento;
	use App\Alumnodevolucione;
	use App\Alumnoextra;
	use App\Metodospago;
	use App\Formaspago;
	use App\Sedesucursale;
	use App\Cuenta;
	use App\Banco;
	use App\Conceptoscargo;
	use App\Conceptosabono;
	use App\Conceptosdescuento;
	use App\Conceptosextra;
	use App\Conceptosdevolucione;
	use App\Egreso;
	use App\Ingreso;
	use App\Sucursale;
	use App\Calendario;
	use App\Cupone;
	use App\Fichacupone;
	use App\Alumnofiscale;
	use App\Aspiracione;
	use App\Tipopago;
	use App\Publicitario;
	use App\Categoria;
	use App\Sede;
	use App\Turno;
	use App\Clases\Inscripciones;
	use App\Clases\Cupones;
	use App\Clases\Datospublicitarios;
	use Illuminate\Support\Facades\DB;

	class Fichas{
		function costo($ficha){
			try {
				$costo = Ficha::join('grupos', 'idGrupo', '=', 'grupos.id')->
				join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
				leftjoin('alumnodescuentos', 'fichas.id', '=', 'alumnodescuentos.idFicha')->
				select(
					DB::raw('(altacursos.precio - alumnodescuentos.monto) as descuento')
				)->where('fichas.id', '=', $ficha)->get();
				return (count($costo) > 0) ? $costo[0]->descuento : 0;
			} catch (Exception $e) {
				return null;
			}
		}

		function precio($id){
			try {
				$costo = Ficha::join('grupos', 'idGrupo', '=', 'grupos.id')->
				join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
				select(
					'altacursos.precio'
				)->where('fichas.id', '=', $id)->get();
				return (count($costo) > 0) ? $costo[0]->precio : 0;
			} catch (Exception $e) {
				return null;
			}
		}

		function ficha($id){
			try {
				return Ficha::find($id);
			} catch (Exception $e) {
				return null;
			}
		}

		function alumno($id){
			try {
				$ficha = Ficha::find($id);
				return Alumno::find($ficha->idAlumno);
			} catch (Exception $e) {
				return null;
			}
		}

		function grupo($id){
			try {
				return Ficha::join('grupos', 'fichas.idGrupo', '=', 'grupos.id')->
				join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
				select(
					'altacursos.*',
					'grupos.idHorario',
					'grupos.idTurno',
					'altacursos.id as idAltaCurso',
					'grupos.id as idGrupo',
					'fichas.idSucursalImparticion as idSucursal',
					'fichas.observaciones',
					'fichas.fecha',
					'fichas.idSucursalImparticion',
					'fichas.idSucursalInscripcion'
				)->where('fichas.id', '=', $id)->get()[0];
			} catch (Exception $e) {
				return null;
			}
		}

		function listas(){
			try {
				$inscripciones = new Inscripciones();
				return array(
					'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'subniveles' => Subnivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'categorias' => Categoria::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'modalidades' => Modalidade::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'cursos' => Curso::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'sedes' => Sede::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'turnos' => Turno::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'horarios' => Horario::select(DB::raw("CONCAT(inicio, ' - ', fin) as nombre"), 'id')->where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'sucursales' => Sucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'calendarios' => Calendario::whereRaw('fin > NOW()')->where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'sedessucursales' => Sedesucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'grupos' => Grupo::join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
			            join('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
			            join('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
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
			                'cursos.nombre as curso',
			                'grupos.idHorario', 
			                'grupos.idTurno')->
			            whereRaw('calendarios.fin > NOW()')->get(),
			        'cupos' => $inscripciones->cupos()
				);
			} catch (Exception $e) {
				return null;
			}
		}

		function modificar($id, $grupoID, $observaciones, $calendarioID, $sucursalID){
			try {
				$ficha = Ficha::find($id);
				$ficha->idGrupo = $grupoID;
				$ficha->observaciones = $observaciones;
				$ficha->idCalendario = $calendarioID;
				$ficha->idSucursalImparticion = $sucursalID;
				$ficha->save();
				return $ficha;
			} catch (Exception $e) {
				return null;
			}
		}

		function actualizarNumeroRegistro($id, $registro){
	      try {
	        $dato = Ficha::find($id);
	        $dato->numeroRegistro = $registro;
	        $dato->save();
	        return $dato;
	      } catch (Exception $e) {
	        return null;
	      }
	  	}

	  	function actualizarEstatus($id, $estatus){
	  		try {
	  			$dato = Ficha::find($id);
	  			$dato->estatus = $estatus;
	  			$dato->save();
	  			return $dato;
	  		} catch (Exception $e) {
	  			return null;
	  		}
	  	}

	  	function activarEstadoCuenta($id){
	  		try {
	  			Alumnoabono::where('idFicha', '=', $id)->update(['activo' => 0]);
	  			Alumnodescuento::where('idFicha', '=', $id)->update(['activo' => 0]);
	  			Alumnocargo::where('idFicha', '=', $id)->update(['activo' => 0]);
	  			Alumnoextra::where('idFicha', '=', $id)->update(['activo' => 0]);
	  			Alumnodevolucione::where('idFicha', '=', $id)->update(['activo' => 0]);
	  			return true;
	  		} catch (Exception $e) {
	  			return false;
	  		}
	  	}

	  	function desactivarEstadoCuenta($id){
	  		try {
	  			Alumnoabono::where('idFicha', '=', $id)->update(['activo' => 1]);
	  			Alumnodescuento::where('idFicha', '=', $id)->update(['activo' => 1]);
	  			Alumnocargo::where('idFicha', '=', $id)->update(['activo' => 1]);
	  			Alumnoextra::where('idFicha', '=', $id)->update(['activo' => 1]);
	  			Alumnodevolucione::where('idFicha', '=', $id)->update(['activo' => 1]);
	  			return true;
	  		} catch (Exception $e) {
	  			return null;
	  		}
	  	}
	}
?>