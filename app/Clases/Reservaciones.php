<?php  

  namespace App\Clases;
  use App\Cursosparidade;
  use App\Calendario;
  use App\Sucursale;
  use App\Paridade;
  use App\Altacurso;
  use App\Grupo;
  use App\Aula;
  use App\Reservacionesaula;
  use Illuminate\Support\Facades\DB;

  class Reservaciones{

  	function listas($sucursalID){
  		try {
  			return array(
  				'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->whereRaw('fin > NOW()')->get(),
  				'sucursales' => Sucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'paridades' => Paridade::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'aulas' => Aula::where('eliminado', '=', 0)->where('activo', '=', 1)->where('idSucursal', '=', $sucursalID)->get()
  			);
  		} catch (Exception $e) {
  			return null;
  		}
  	}

    function aulas($grupos, $sucursalID){
      try {
        foreach ($grupos as $grupo) {
          $grupo->aulas = Reservacionesaula::where('idSucursal', '=', $sucursalID)->where('idGrupo', '=', $grupo->id)->count();
        }
        return $grupos;
      } catch (Exception $e) {
        return null;
      }
    }

    function reservadas($grupoID, $sucursalID, $calendarioID){
      try {
        return Reservacionesaula::join('aulas', 'reservacionesaulas.idAula', '=', 'aulas.id')->
          select('aulas.nombre as aula', 'reservacionesaulas.id', 'aulas.cupo')->
          where('reservacionesaulas.idSucursal', '=', $sucursalID)->
          where('idGrupo', '=', $grupoID)->
          where('idCalendario', '=', $calendarioID)->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function reservar($aulaID, $calendarioID, $sucursalID, $grupoID){
      try {
        return Reservacionesaula::create([
          'idAula' => $aulaID,
          'idCalendario' => $calendarioID,
          'idSucursal' => $sucursalID,
          'idGrupo' => $grupoID,
          'activo' => 1,
          'eliminado' => 0
        ]);
      } catch (Exception $e) {
        return null;
      }
    }

    function liberar($id){
      try {
        $dato = Reservacionesaula::find($id);
        $dato->delete();
        return $dato;
      } catch (Exception $e) {
        return null;
      }
    }

    function existe($aulaID, $calendarioID, $sucursalID, $grupoID){
      try{
        return Reservacionesaula::where('idAula', '=', $aulaID)->
        where('idCalendario', '=', $calendarioID)->
        where('idSucursal', '=', $sucursalID)->
        where('idGrupo', '=', $grupoID)->count() > 0;

      }catch(Exception ){
        return null;
      }
    }
  }

?>