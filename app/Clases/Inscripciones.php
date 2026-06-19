<?php

	namespace App\Clases;
	use Carbon\Carbon;
	use Illuminate\Support\Facades\DB;
	use App\Sexo;
	use App\Calendario;
	use App\Nivele;
	use App\Subnivele;
	use App\Categoria;
	use App\Modalidade;
	use App\Curso;
	use App\Sede;
	use App\Turno;
	use App\Horario;
	use App\Sucursale;
	use App\Sedesucursale;
	use App\Grupo;
	use App\Estado;
	use App\Municipio;
	use App\Escuela;
	use App\Tipoescuela;
	use App\Universidade;
	use App\Centrosuniversitario;
	use App\Carrera;
	use App\Medioscontacto;
	use App\Mediospublicitario;
	use App\Viaspublicitaria;
	use App\Motivosinscripcione;
	use App\Motivosbachillerato;
	use App\Campania;
	use App\Empresascurso;
	use App\Metodospago;
	use App\Formaspago;
	use App\Banco;
	use App\Cuenta;
	use App\Conceptosabono;
	use App\Conceptoscargo;
	use App\Conceptosdescuento;
	use App\Tipopago;
	use App\Ficha;
	use App\Reservacionesaula;
	use App\Tutore;
	use App\Alumnodomicilio;
	use App\Alumno;
	use App\Publicitario;
	use App\Aspiracione;
	use App\Datosescolare;
	use App\Alumnocargo;
	use App\Alumnoabono;
	use App\Alumnodescuento;
	use App\Bloqueohorario;

	use App\Clases\Alumnos;
	use App\Clases\Folios;
	use App\Clases\Ingresos;


	class Inscripciones{
		function listasAlumnos(){
			try {
				return array(
					'sexos' => Sexo::where('activo', '=', 1)->where('eliminado', '=', 0)->get()
				);
			} catch (Exception $e) {
				return null;
			}
		}

		function listasInscripcion(){
			try {
				return array(
					'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'subniveles' => Subnivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'categorias' => Categoria::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'modalidades' => Modalidade::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'cursos' => Curso::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'sedes' => Sede::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'turnos' => Turno::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'horarios' => Horario::select(
						'horarios.*',
						DB::raw("CONCAT(horarios.inicio, ' - ', horarios.fin) as nombre")
					)->
					where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'sucursales' => Sucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'sedessucursales' => Sedesucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
				);
			} catch (Exception $e) {
				return null;
			}
		}

		function listasDomicilio(){
			try {
				return array(
					'estados' => Estado::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'municipios' => Municipio::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
				);
			} catch (Exception $e) {
				return null;
			}
		}

		function listasEscolares(){
			try {
				return array(
					'escuelas' => Escuela::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'tipos' => Tipoescuela::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'universidades' => Universidade::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'centros' => Centrosuniversitario::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'carreras' => Carrera::join('calendarios', 'idCalendario', '=', 'calendarios.id')->select('carreras.*')->where('calendarios.fin', '>', Carbon::now())->where('carreras.eliminado', '=', 0)->get(),
					'estados' => Estado::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'municipios' => Municipio::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
				);
			} catch (Exception $e) {
				return null;
			}
		}

		function listasPublicitarios(){
			try {
				return array(
					'contacto' => Medioscontacto::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'medios' => Mediospublicitario::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'vias' => Viaspublicitaria::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'motivos' => Motivosinscripcione::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'bachillerato' => Motivosbachillerato::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'campanias' => Campania::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'empresas' => Empresascurso::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
				);
			} catch (Exception $e) {
				return null;
			}
		}

		function listasCuenta(){
			try {
				return array(
					'metodos' => Metodospago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'formas' => Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'bancos' => Banco::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'cuentas' => Cuenta::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'abonos' => Conceptosabono::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'cargos' => Conceptoscargo::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'descuentos' => Conceptosdescuento::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'tipos' => Tipopago::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
					'cursos' => Curso::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
				);
			} catch (Exception $e) {
				return null;
			}
		}

		function fichas($calendarioID, $sucursalID){
			try {
				return Ficha::leftjoin('grupos', 'idGrupo', '=', 'grupos.id')->
	            leftjoin('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
	            leftjoin('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
	            leftjoin('niveles', 'altacursos.idNivel', '=', 'niveles.id')->
	            leftjoin('subniveles', 'altacursos.idSubnivel', '=', 'subniveles.id')->
	            leftjoin('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
	            leftjoin('categorias', 'altacursos.idCategoria', '=', 'categorias.id')->
	            leftjoin('usuarios', 'idUsuario', '=', 'usuarios.id')->
	            leftJoin('alumnos', 'fichas.idAlumno', '=', 'alumnos.id')->
	            leftjoin('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
	            select(
	                'fichas.id', 
	                'fichas.id as ficha',
	                'fichas.folio', 
	                'calendarios.nombre as calendario', 
	                'niveles.nombre as nivel',
	                'subniveles.nombre as subnivel',
	                'cursos.nombre as curso',
	                'categorias.nombre as categoria',
	                DB::raw("CONCAT(alumnos.nombre, ' ', alumnos.apellidoPaterno, ' ', alumnos.apellidoMaterno) as alumno"),
	                DB::raw("(CASE 
	                    WHEN(empleados.idDepartamento = 6) THEN 'bg-verde'
	                    END) AS bg"),
	                DB::raw("(CASE 
	                    WHEN(empleados.idDepartamento = 6) THEN empleados.nombre
	                    END) AS title")
	               )->
	            where('fichas.idCalendario', '=', $calendarioID)->
	            where('fichas.idSucursalInscripcion', '=', $sucursalID)->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function grupos(){
			try {
				return Grupo::join('altacursos', 'idAltaCurso', '=', 'altacursos.id')->
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
	                'grupos.idTurno',
	            )->
	            where('calendarios.fin', '>', Carbon::now())->get();
			} catch (Exception $e) {
				return null;
			}
		}

		function cupos(){
			try {
				$cupos = Reservacionesaula::join('grupos', 'reservacionesaulas.idGrupo', '=', 'grupos.id')->
				join('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
				join('cursosparidades', 'altacursos.idCurso', '=', 'cursosparidades.idCurso')->
				join('aulas', 'reservacionesaulas.idAula', '=', 'aulas.id')->
				join('calendarios', 'altacursos.idCalendario', '=', 'calendarios.id')->
				select(
					'grupos.id as id',
	                'altacursos.idCalendario',
	                'altacursos.idNivel',
	                'altacursos.idSubnivel',
	                'altacursos.idCategoria',
	                'altacursos.idModalidad',
	                'altacursos.idSede',
	                'altacursos.idCurso',
	                'grupos.idHorario', 
	                'grupos.idTurno',
	                'aulas.cupo',
	                'aulas.idSucursal',
	                'cursosparidades.idParidad'
				)->where('calendarios.fin', '>', Carbon::now())->
				orderBy('grupos.id', 'DESC')->get();
				foreach ($cupos as $grupo) {
	           		if($grupo->cupo > 0){
	           			if(is_null($grupo->idParidad)){
		           			$grupo->inscritos = Ficha::where('idSucursalImparticion', '=', $grupo->idSucursal)->where('idGrupo', '=', $grupo->id)->where('estatus', '=', 1)->count();
		           		}else{
		           			$inscritos = 0;
		           			foreach ($cupos as $group) {
		           				if(intval($grupo->idCalendario) === intval($group->idCalendario) && intval($grupo->idTurno) === intval($group->idTurno) && intval($grupo->idHorario) === intval($group->idHorario) && intval($grupo->idNivel) === intval($group->idNivel) &&intval($grupo->idSubnivel) === intval($group->idSubnivel) && intval($grupo->idModalidad) === intval($group->idModalidad) && intval($grupo->idCategoria) === intval($group->idCategoria) && intval($grupo->idSede) === intval($group->idSede) && intval($grupo->idParidad) === intval($group->idParidad)){
		           					$inscritos = $inscritos = Ficha::where('idSucursalImparticion', '=', $grupo->idSucursal)->where('idGrupo', '=', $group->id)->where('estatus', '=', 1)->count();
		           				}
		           			}
		           			$grupo->inscritos = $inscritos;
		           		}
	           		}else{
	           			$grupo->inscritos = Ficha::where('idGrupo', '=', $grupo->id)->where('estatus', '=', 1)->count();
	           		}
	           	}
				return $cupos;
			} catch (Exception $e) {
				return null;			}
		}

		function nuevoAlumno($datos){
			try {
				$alumnos = new Alumnos();
				return Alumno::create([
		          'nombre' => $datos['nombre'],
		          'apellidoPaterno' => $datos['apellidoPaterno'],
		          'apellidoMaterno' => $datos['apellidoMaterno'],
		          'telefono' => $datos['telefono'],
		          'celular' => $datos['celular'],
		          'correo' => $datos['correo'],
		          'idSexo' => $datos['idSexo'],
		          'fechaNacimiento' => $datos['fechaNacimiento'],
		          'codigo' => $alumnos->codigo($datos['nombre'], $datos['apellidoPaterno'], $datos['apellidoMaterno'], $datos['fechaNacimiento']),
		          'activo' => 1,
		          'eliminado' => 0
		        ]);
			} catch (Exception $e) {
				return null;
			}
		}

		function nuevoTutor($datos){
			try {
				return Tutore::create([
					'idAlumno' => $datos['idAlumno'],
					'nombre' => $datos['nombre'],
					'celular' => $datos['celular'],
					'telefono' => $datos['telefono'],
					'activo' => 1,
					'eliminado' => 0
				]);
			} catch (Exception $e) {
				return null;
			}
		}

		function nuevoDomicilioAlumno($datos){
			try {
				return Alumnodomicilio::create([
					'idAlumno' => $datos['idAlumno'],
					'calle' => $datos['calle'],
					'numeroExterior' => $datos['numeroExterior'],
					'numeroInterior' => $datos['numeroInterior'],
					'colonia' => $datos['colonia'],
					'codigoPostal' => $datos['codigoPostal'],
					'idEstado' => $datos['idEstado'],
					'idMunicipio' => $datos['idMunicipio'],
					'activo' => 1,
					'eliminado' => 0
				]);
			} catch (Exception $e) {
				return null;
			}
		}

		function nuevaFicha($datos){
			try {
				$folios = new Folios();
				return Ficha::create([
					'idAlumno' => $datos['idAlumno'],
					'idGrupo' => $datos['idGrupo'],
					'semana' => date('W'),
					'idSucursalImparticion' => $datos['idSucursalImparticion'],
					'idSucursalInscripcion' => (intval($datos['idSucursalInscripcion']) === 1) ? $datos['idSucursalImparticion'] : $datos['idSucursalInscripcion'],
					'idCalendario' => $datos['idCalendario'],
					'idUsuario' => $datos['idUsuario'],
					'idUsuarioInformacion' => $datos['idUsuarioInformacion'],
					'idTipoPago' => 0,
					'idNivel' => $datos['idNivel'],
					'folio' => $folios->proximoFicha($datos['idCalendario'], $datos['idNivel'], $datos['idSucursalImparticion']),
					'intentos' => $datos['intentos'],
					'observaciones' => $datos['observaciones'],
					'fecha' => Carbon::now(),
					'activo' => 1,
					'eliminado' => 0
				]);
			} catch (Exception $e) {
				return null;
			}
		}

		function nuevoPublicitarios($datos){
			try {
				return Publicitario::create([
					'idFicha' => $datos['idFicha'],
					'idMedioContacto' => $datos['idMedioContacto'],
	                'idMedioPublicitario' => $datos['idMedioPublicitario'],
	                'idViaPublicitaria' => $datos['idViaPublicitaria'],
	                'idMotivoInscripcion' => $datos['idMotivoInscripcion'],
	                'idCampania' => $datos['idCampania'],
	                'idMotivoBachillerato' => $datos['idMotivoBachillerato'],
	                'idEmpresaCurso' => $datos['idEmpresa'],
	                'tomoCurso' => $datos['curso'],
	                'eliminado' => 0,
	                'activo' => 1
				]);
			} catch (Exception $e) {
				return null;
			}
		}

		function nuevoAspiracion($datos){
			try {
				return Aspiracione::create([
					'idFicha' => $datos['idFicha'],
					'idUniversidad' => $datos['idUniversidad'],
					'idCentroUniversitario' => $datos['idCentroUniversitario'],
					'idCarrera' => $datos['idCarrera'],
					'activo' => 1,
					'eliminado' => 0
				]);
			} catch (Exception $e) {
				return null;
			}
		}

		function nuevoEscolares($datos){
			try {
				return Datosescolare::create([
					'idAlumno' => $datos['idAlumno'],
					'idTipoEscuela' => $datos['idTipoEscuela'],
					'idEscuela' => $datos['idEscuela'],
					'idEstado' => $datos['idEstado'],
					'idMunicipio' => $datos['idMunicipio'],
					'idSubnivel' => $datos['idSubnivel'],
					'promedio' => $datos['promedio'],
					'activo' => 1,
					'eliminado' => 0
				]);
			} catch (Exception $e) {
				return null;
			}
		}

		function agregarCargos($datos, $fichaID, $usuarioID){
			try {
				$contador = 0;
				foreach ($datos as $dato) {
					$cargo = Alumnocargo::create([
						'idFicha' => $fichaID,
						'monto' => $dato['monto'],
						'concepto' => $dato['concepto'],
						'idUsuario' => $usuarioID,
						'idConcepto' => $dato['idConcepto'],
						'activo' => 1,
						'eliminado' => 0
					]);
					$contador++;
				}
				return $contador;
			} catch (Exception $e) {
				return null;
			}
		}

		function agregarAbonos($datos, $fichaID, $inscripcion, $sucursalID, $usuarioID){
			try {
				$ingresos = new Ingresos();
				$contador = 0;
				$folios = new Folios();
				$abonos = array();
				foreach ($datos as $dato) {
					$folio = $folios->proximoIngreso($inscripcion['idNivel'], $inscripcion['idCalendario'], $sucursalID);
					$ingreso = $ingresos->nuevo($dato['concepto'], $dato['monto'], 'Pago de incripcion', 1, 1, $sucursalID, $inscripcion['idCalendario'], $dato['idFormaPago'], $dato['idMetodoPago'], $usuarioID, 2, $inscripcion['idNivel'], $folio, $dato['imagen'], $dato['idBanco'], $dato['referencia'], $dato['nombre'], $dato['idCuenta'], Carbon::now()
					);

					$abono = Alumnoabono::create([
						'idFicha' => $fichaID,
						'idIngreso' => $ingreso->id,
						'idUsuario' => $usuarioID,
						'monto' => $dato['monto'],
						'concepto' => $dato['concepto'],
						'idMetodoPago' => $dato['idMetodoPago'],
						'idFormaPago' => $dato['idFormaPago'],
						'idConcepto' => $dato['idConcepto'],
						'activo' => 1,
						'eliminado' => 0
					]);
					$contador++;
					$abonos[] = $abono;
				}
				return $abonos;
			} catch (Exception $e) {
				return null;
			}
		}

		function agregarDescuentos($datos, $fichaID, $usuarioID){
			try {
				$contador = 0;
				foreach ($datos as $dato) {
					$descuento = Alumnodescuento::create([
						'idFicha' => $fichaID,
						'monto' => $dato['monto'],
						'concepto' => 'Descuento',
						'idUsuario' => $usuarioID,
						'idCupon' => $dato['idCupon'],
						'tipo' => ( is_null($dato['idCupon'])) ? 0 : $dato['idTipo'],
						'cantidad' => $dato['cantidad'],
						'idConcepto' => $dato['idConcepto'],
						'activo' => 1,
						'eliminado' => 0
					]);
				}
				return $contador;
			} catch (Exception $e) {
				return 0;
			}
		}

		function codigos(){
			try {
				$alumnos = Alumno::all();
				foreach ($alumnos as $alumno) {
					$alumno->codigo = substr($alumno->codigo, 0, 14);
				}
				return $alumnos;
			} catch (Exception $e) {
				return null;
			}
		}

		function existeBloqueo($grupoID, $sucursalID){
			try {
				return (Bloqueohorario::where('idGrupo', '=', $grupoID)->where('idSucursal', '=', $sucursalID)->count() > 0);
			} catch (Exception $e) {
				return null;
			}
		}
	}
?>