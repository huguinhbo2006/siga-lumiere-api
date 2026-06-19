<?php  

  namespace App\Clases;
  use App\Clases\Grupos;
  use App\Sucursale;
  use App\Calendario;
  use App\Nivele;
  use App\Subnivele;
  use App\Categoria;
  use App\Modalidade;
  use App\Curso;
  use App\Sede;
  use App\Turno;
  use App\Sedesucursale;
  use App\Horario;
  use App\Bloqueohorario;

  use Illuminate\Support\Facades\DB;

  class Bloqueos{

  	function listas($sucursalID){
  		try {
  			$grupos = new Grupos();
  			return array(
  				'grupos' => $grupos->actuales($sucursalID),
  				'sucursales' => Sucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'subniveles' => Subnivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'categorias' => Categoria::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'modalidades' => Modalidade::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'cursos' => Curso::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'sedes' => Sede::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'turnos' => Turno::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'horarios' => Horario::select(
  					'id',
  					DB::raw("CONCAT(inicio, ' - ', fin) as nombre")
  				)->where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'sedessucursales' => Sedesucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
  			);
  		} catch (Exception $e) {
  			return null;
  		}
  	}

    function bloquear($grupoID, $sucursalID){
      try {
        $existe = Bloqueohorario::where('idGrupo', '=', $grupoID)->where('idSucursal', '=', $sucursalID)->get();
        if(count($existe) > 0){
            foreach ($existe as $eliminar) {
                $delete = Bloqueohorario::find($eliminar->id);
                $delete->delete();
            }
        }
        $bloqueo = Bloqueohorario::create([
            'idGrupo' => $grupoID,
            'idSucursal' => $sucursalID,
            'activo' => 1,
            'eliminado' => 0
        ]);
        return $bloqueo;
      } catch (Exception $e) {
        return null;
      }
    }

    function desbloquear($id){
      try {
        return Bloqueohorario::find($id)->delete();
      } catch (Exception $e) {
        return null;
      }
    }

  }

?>