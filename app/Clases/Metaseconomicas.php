<?php  

  namespace App\Clases;
  use App\Empleado;
  use App\Usuario;
  use App\Calendario;
  use App\Metaseconomica;
  use App\Ingreso;
  use Illuminate\Support\Facades\DB;

  class Metaseconomicas{
  	function listas(){
      try {
        return array(
          'usuarios' => Usuario::join('empleados', 'idEmpleado', '=', 'empleados.id')->select('usuarios.id', 'empleados.nombre')->where('usuarios.eliminado', '=', 0)->where('usuarios.activo', '=', 1)->where('usuarios.idTipoUsuario', '=', 8)->get(),
          'calendarios' => Calendario::whereRaw('fin > NOW()')->where('eliminado', '=', 0)->where('activo', '=', 1)->get() 
        ); 
      } catch (Exception $e) {
        return null;
      }
  	}

    function mostrar($calendarioID){
      try {
        return Metaseconomica::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
        join('usuarios', 'idUsuario', '=', 'usuarios.id')->
        join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
        select(
          'metaseconomicas.*',
          'calendarios.nombre as calendario',
          'empleados.nombre as empleado',
          DB::raw('ELT(metaseconomicas.mes, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") as mont')
        )->
        where('idCalendario', '=', $calendarioID)->where('metaseconomicas.eliminado', '=', 0)->where('metaseconomicas.activo', '=', 1)->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function nuevo($usuarioID, $calendarioID, $mes, $meta){
      try {
        return Metaseconomica::create([
            'idCalendario' => $calendarioID,
            'idUsuario' => $usuarioID,
            'mes' => $mes,
            'meta' => $meta,
            'activo' => 1,
            'eliminado' => 0
        ]);
      } catch (Exception $e) {
        return null;
      }
    }

    function modificar($id, $goal){
      try {
        $meta = Metaseconomica::find($id);
        $meta->meta = $goal;
        $meta->save();
        return $meta;
      } catch (Exception $e) {
        return null;
      }
    }

    function completar($id){
      try {
        return Metaseconomica::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
        join('usuarios', 'idUsuario', '=', 'usuarios.id')->
        join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
        select(
          'metaseconomicas.*',
          'calendarios.nombre as calendario',
          'empleados.nombre as empleado',
          DB::raw('ELT(metaseconomicas.mes, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") as mont')
        )->where('metaseconomicas.id', '=', $id)->get()[0];
      } catch (Exception $e) {
        return null;
      }
    }

    function existe($usuarioID, $calendarioID, $mes, $meta){
      try {
        $existe = Metaseconomica::where('idUsuario', '=', $usuarioID)->where('idCalendario', '=', $calendarioID)->where('mes', '=', $mes)->count();
        return !($existe > 0);
      } catch (Exception $e) {
        return null;
      }
    }

    function traer($usuarioID, $calendarioID){
        try {
          $datos = Metaseconomica::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
          join('usuarios', 'idUsuario', '=', 'usuarios.id')->
          join('empleados', 'usuarios.idEmpleado', '=', 'empleados.id')->
          select(
            'metaseconomicas.*',
            'calendarios.nombre as calendario',
            'empleados.nombre as texto',
            DB::raw('ELT(metaseconomicas.mes, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") as mont')
          )->
          where('idCalendario', '=', $calendarioID)->where('idUsuario', '=', $usuarioID)->whereRaw('mes = MONTH(NOW())')->get();

          foreach ($datos as $dato) {
            $dato->ingreso = Ingreso::where('idCalendario', '=', $calendarioID)->where('idUsuario', '=', $usuarioID)->whereRaw('MONTH(created_at) = MONTH(NOW())')->sum('monto');
          }
          return $datos;
        } catch (Exception $e) {
            return null;
        }
    }

    function obtener($calendarioID){
      try {
        $usuarios = Usuario::join('empleados', 'idEmpleado', '=', 'empleados.id')->
        select(
          'usuarios.id',
          'empleados.nombre as nombre'
        )->where('idTipoUsuario', '=', 8)->get();

        $respuesta = array();
        foreach ($usuarios as $usuario) {
          $meta = $this->traer($usuario->id, $calendarioID);
          if(count($meta) > 0){
            $respuesta[] = $meta[0];
          }
        }
        return $respuesta;
      } catch (Exception $e) {
        return null;
      }
    }
  }

?>