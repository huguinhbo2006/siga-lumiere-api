<?php  

  namespace App\Clases;
  use App\Alumno;
  use App\Ficha;
  use App\Tutore;
  use App\Alumnodomicilio;
  use App\Datosescolare;
  use App\Estado;
  use App\Municipio;
  use App\Subnivele;
  use App\Tipoescuela;
  use App\Escuela;
  use App\Fichadocumento;
  use App\Aspiracione;
  use App\Publicitario;
  use Illuminate\Support\Facades\DB;

  class Alumnos{
    function listas(){
      try {
        return array(
          'estados' => Estado::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'municipios' => Municipio::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'subniveles' => Subnivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'tipos' => Tipoescuela::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'escuelas' => Escuela::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
        );
      } catch (Exception $e) {
        return null;
      }
    }

    function codigo($nombre, $apellidoPaterno, $apellidoMaterno, $fechaNacimiento){
      try {
        $codigoAlumno = substr($nombre, 0, 2).substr($apellidoPaterno,0 ,2).substr($apellidoMaterno,0 ,2).$fechaNacimiento;
        $codigoAlumno = str_replace('-', '', $codigoAlumno);
        $existe = Alumno::where('codigo', 'LIKE', '%'.$codigoAlumno."%")->count();
        $homoclave = ($existe > 9) ? $existe+1 : '0'.($existe+1);
        return strtoupper($codigoAlumno."LUM".$homoclave);
      } catch (Exception $e) {
        return null;
      }
    }

    function nuevo($nombre, $apellidoPaterno, $apellidoMaterno, $telefono, $celular, $correo, $sexoID, $fechaNacimiento){
      try {
        return Alumno::create([
          'nombre' => $nombre,
          'apellidoPaterno' => $apellidoPaterno,
          'apellidoMaterno' => $apellidoMaterno,
          'telefono' => $telefono,
          'celular' => $celular,
          'correo' => $correo,
          'idSexo' => $sexoID,
          'fechaNacimiento' => $fechaNacimiento,
          'codigo' => $this->codigo($nombre, $apellidoPaterno, $apellidoMaterno, $fechaNacimiento),
          'activo' => 1,
          'eliminado' => 0
        ]);
      } catch (Exception $e) {
        return null;
      }
    }

    function inscritosUsuario($usuarioID){
      try {
        return Ficha::join('alumnos', 'idAlumno', '=', 'alumnos.id')->
        select(
          DB::raw('CONCAT(alumnos.nombre, " ", alumnos.apellidoPaterno, " ", alumnos.apellidoMaterno) as nombre'),
          'alumnos.id'
        )->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function buscar($busqueda){
      try {
        return Alumno::whereRaw("CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno) LIKE '%$busqueda%' OR celular LIKE '%$busqueda%' OR codigo LIKE '%$busqueda%'")->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function nombre($id){
      try {
        return Alumno::select(DB::raw("CONCAT(nombre, ' ', apellidoPaterno, ' ',apellidoMaterno) as nombre"))->where('id', '=', $id)->get()[0]->nombre;
      } catch (Exception $e) {
        return null;
      }
    }

    function personales($id){
      try{
        return Alumno::find($id);
      }catch(Exception ){
        return null;
      }
    }

    function tutor($alumnoID){
      try {
        return Tutore::where('idAlumno', '=', $alumnoID)->get()[0];
      } catch (Exception $e) {
        return null;
      }
    }

    function domicilio($alumnoID){
      try {
        return Alumnodomicilio::where('idAlumno', '=', $alumnoID)->get()[0];
      } catch (Exception $e) {
        return null;
      }
    }

    function escolares($alumnoID){
      try{
        return Datosescolare::where('idAlumno', '=', $alumnoID)->get()[0];
      }catch(Exception ){
        return null;
      }
    }

    function modificarPersonales($id, $telefono, $celular, $correo, $nacimiento){
      try {
        $alumno = Alumno::find($id);
        $alumno->telefono = $telefono;
        $alumno->celular = $celular;
        $alumno->correo = $correo;
        $alumno->fechaNacimiento = $nacimiento;
        $alumno->save();
        return $alumno;
      } catch (Exception $e) {
        return null;
      }
    }

    function modificarTutor($alumnoID, $telefono, $celular, $nombre){
      try {
        $tutor = Tutore::where('idAlumno', '=', $alumnoID)->get()[0];
        $tutor->telefono = $telefono;
        $tutor->celular = $celular;
        $tutor->nombre = $nombre;
        $tutor->save();
        return $tutor;
      } catch (Exception $e) {
        return null;
      }
    }

    function modificarDomicilio($alumnoID, $calle, $exterior, $interior, $colonia, $codigo, $estadoID, $municipioID){
      try {
        $domicilio = Alumnodomicilio::where('idAlumno', '=', $alumnoID)->get()[0];
        $domicilio->calle = $calle;
        $domicilio->numeroExterior = $exterior;
        $domicilio->numeroInterior = $interior;
        $domicilio->colonia = $colonia;
        $domicilio->codigoPostal = $codigo;
        $domicilio->idEstado = $estadoID;
        $domicilio->idMunicipio = $municipioID;
        $domicilio->save();
        return $domicilio;
      } catch (Exception $e) {
        return null;
      }
    }

    function modificarEscolares($alumnoID, $subnivelID, $tipoID, $escuelaID, $estadoID, $municipioID, $promedio){
      try{
        $escolares = Datosescolare::where('idAlumno', '=', $alumnoID)->get()[0];
        $escolares->idSubnivel = $subnivelID;
        $escolares->idTipoEscuela = $tipoID;
        $escolares->idEscuela = $escuelaID;
        $escolares->idEstado = $estadoID;
        $escolares->idMunicipio = $municipioID;
        $escolares->promedio = $promedio;
        $escolares->save();
        return $escolares;
      }catch(Exception ){
        return null;
      }
    }

    function fichas($id){
      try {
        return Ficha::leftjoin('grupos', 'idGrupo', '=', 'grupos.id')->
                leftjoin('altacursos', 'grupos.idAltaCurso', '=', 'altacursos.id')->
                leftjoin('niveles', 'altacursos.idNivel', '=', 'niveles.id')->
                leftjoin('subniveles', 'altacursos.idSubnivel', '=', 'subniveles.id')->
                leftjoin('modalidades', 'altacursos.idModalidad', '=', 'modalidades.id')->
                leftjoin('categorias', 'altacursos.idCategoria', '=', 'categorias.id')->
                leftjoin('cursos', 'altacursos.idCurso', '=', 'cursos.id')->
                leftjoin('turnos', 'grupos.idTurno', '=', 'turnos.id')->
                leftjoin('horarios', 'grupos.idHorario', '=', 'horarios.id')->
                leftjoin('cupones', 'cupones.idFicha', '=', 'fichas.id')->
                select(
                    'fichas.folio as folio',
                    'cursos.icono',
                    'cursos.nombre as curso',
                    'niveles.nombre as nivel',
                    'subniveles.nombre as subnivel',
                    'modalidades.nombre as modalidad',
                    'categorias.nombre as categoria',
                    'turnos.nombre as turno',
                    DB::raw('CONCAT(horarios.inicio, " - ", horarios.fin) as horario'),
                    'altacursos.inicio',
                    'altacursos.fin',
                    'altacursos.limitePago',
                    'altacursos.precio',
                    'fichas.id',
                    'fichas.numeroRegistro',
                    'fichas.estatus',
                    'altacursos.idCalendario',
                    'altacursos.idNivel',
                    'grupos.id as idGrupo',
                    DB::raw("(CASE 
                        WHEN(fichas.estatus = 1) THEN 'bg-verde'
                        WHEN(fichas.estatus = 2) THEN 'bg-amarillo'
                        WHEN(fichas.estatus = 3) THEN 'bg-rojo'
                        WHEN(fichas.estatus = 4) THEN 'bg-amarillo'
                        WHEN(fichas.estatus = 5) THEN 'bg-rojo'
                        WHEN(fichas.estatus <> 1 AND fichas.estatus <> 0 AND NOW() > altacursos.fin) THEN 'bg-blue'
                        END) AS fondo"),
                    DB::raw("(CASE 
                        WHEN(fichas.estatus = 1) THEN 'Activa'
                        WHEN(fichas.estatus = 2) THEN 'Inasistencia'
                        WHEN(fichas.estatus = 3) THEN 'Congelada'
                        WHEN(fichas.estatus = 4) THEN 'Moroso'
                        WHEN(fichas.estatus = 5) THEN 'Cancelado'
                        WHEN(fichas.estatus <> 1 AND fichas.estatus <> 0 AND NOW() > altacursos.fin) THEN 'Finalizado'
                        END) AS estatusActual"),
                    DB::raw('IF(fichas.estatus = 3, true, false) as congelado'),
                    'cupones.monto as montoCongelado'
        )->where('fichas.idAlumno', '=', $id)->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function completarFichas($fichas){
      try {
        foreach ($fichas as $ficha) {
            $archivos = Fichadocumento::where('idFicha', '=', $ficha->id)->where('eliminado', '=', 0)->get();
            $ficha->archivos = $archivos;
            $datos = Aspiracione::where('idFicha', '=', $ficha->id)->get();
            $ficha->aspiracion = count($datos) > 0 ? $datos[0] : [];
            $datosPublicitarios = Publicitario::where('idFicha', '=', $ficha->id)->get()[0];
            $datosPublicitarios->curso = $datosPublicitarios->tomoCurso;
            $datosPublicitarios->idEmpresa = $datosPublicitarios->idEmpresaCurso;
            $ficha->publicitarios = $datosPublicitarios;
        }
        return $fichas;
      } catch (Exception $e) {
        return null;
      }
    }

  }

?>