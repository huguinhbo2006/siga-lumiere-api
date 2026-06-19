<?php  

  namespace App\Clases;
  use App\Empleado;
  use App\Usuario;
  use Illuminate\Support\Facades\DB;

  class Empleados{

  	function obtener(){
  		try {
  			return Empleado::join('departamentos', 'idDepartamento', '=', 'departamentos.id')->
  			join('sucursales', 'idSucursal', '=', 'sucursales.id')->
  			join('puestos', 'idPuesto', '=', 'puestos.id')->
  			join('usuarios', 'usuarios.idEmpleado', '=', 'empleados.id')->
  			select(
  				'empleados.*',
  				'departamentos.nombre as departamento',
  				'sucursales.nombre as sucursal',
  				'puestos.nombre as puesto',
  				'usuarios.usuario',
  				'usuarios.idTipoUsuario',
  				DB::raw("IF(empleados.activo, '', 'bg-rojo') as bg"),
  			)->where('empleados.eliminado', '=', 0)->
  			where('empleados.id', '<>', 1)->
  			where('departamentos.id', '<>', 1)->get();
  		} catch (Exception $e) {
  			return null;
  		}
  	}

    function vendedores(){
      try {
        return Usuario::join('empleados', 'idEmpleado', '=', 'empleados.id')->
                                  select('usuarios.id as id', 'empleados.nombre as nombre')->
                                  where('empleados.idDepartamento', '=', 6)->
                                  where('empleados.idPuesto', '=', 25)->get();
      } catch (Exception $e) {
        return null;
      }
    }

  }

?>