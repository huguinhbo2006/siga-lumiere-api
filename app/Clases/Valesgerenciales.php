<?php  

  namespace App\Clases;
  use App\Valesgerenciale;
  use App\Nivele;
  use App\Calendario;
  use App\Egreso;
  use App\Solicitudesvalesgerenciale;
  use App\Clases\Sucursales;
  use Illuminate\Support\Facades\DB;

  class Valesgerenciales{

  	function listas(){
  		try {
  			return array(
  				'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->get() 
  			);
  		} catch (Exception $e) {
  			return null;
  		}
  	}

    function crearEgreso($datos, $sucursalID, $usuarioID){
      try {
        $folios = new Folios();

        $folio = $folios->proximoEgreso($datos['idNivel'], $datos['idCalendario'], $sucursalID);
        return Egreso::create([
                'concepto' => 'Vale Gerencial',
                'monto' => $datos['monto'],
                'observaciones' => $datos['observaciones'],
                'idRubro' => 2,
                'idTipo' => 2,
                'idSucursal' => $sucursalID,
                'idCalendario' => $datos['idCalendario'],
                'idFormaPago' => 1,
                'idUsuario' => $usuarioID,
                'idNivel' => $datos['idNivel'],
                'folio' => $folio,
                'referencia' => 4,
                'activo' => 1,
                'eliminado' => 0,
            ]);
      } catch (Exception $e) {
        return null;
      }
    }

  	function mostrar($sucursalID){
  		try {
  			return Valesgerenciale::leftjoin('calendarios', 'idCalendario', '=', 'calendarios.id')->
                    leftjoin('niveles', 'idNivel', '=', 'niveles.id')->
                    leftJoin('solicitudesvalesgerenciales', 'valesgerenciales.id', '=', 'solicitudesvalesgerenciales.idVale')->
                    select(
                        'valesgerenciales.*',
                        'calendarios.nombre as calendario',
                        'solicitudesvalesgerenciales.id as idSolicitud',
                        DB::raw("(CASE 
                            WHEN(valesgerenciales.estatus = 1) THEN 'bg-verde'
                            WHEN(valesgerenciales.estatus = 2) THEN 'bg-rojo'
                            WHEN(valesgerenciales.estatus = 3) THEN 'bg-amarillo'
                            END) AS bg")
                        )->
                    where('valesgerenciales.idSucursal', '=', $sucursalID)->
                    where('valesgerenciales.eliminado', '=', 0)->get();
  		} catch (Exception $e) {
  			return null;
  		}
  	}

    function crearVale($datos, $egreso){
      try {
        $folios = new Folios();

        $folio = $folios->proximoValeGerencial($datos['idCalendario'], $datos['sucursalID']);
        $vale = Valesgerenciale::create([
                'idSucursal' => $datos['sucursalID'],
                'monto' => $datos['monto'],
                'aceptado' => 0,
                'idCalendario' => $datos['idCalendario'],
                'observaciones' => $datos['observaciones'],
                'idUsuarioCreo' => $datos['usuarioID'],
                'idUsuarioRetorno' => 0,
                'idEgreso' => $egreso->id,
                'idIngreso' => 0,
                'folio' => $folio,
                'idNivel' => $datos['idNivel'],
                'estatus' => 1,
                'activo' => 1,
                'eliminado' => 0
            ]);
        $vale->bg = (intval($vale->estatus) === 1) ? 'bg-verde' : 'bg-rojo';
        return $vale;
      } catch (Exception $e) {
        return null;
      }
    }

    function crearSolicitud($datos){
      try {
        $solcitud = Solicitudesvalesgerenciale::create([
                'idVale' => $datos['id'],
                'monto' => $datos['monto'],
                'observaciones' => $datos['observaciones'],
                'activo' => 1,
                'eliminado' => 0
            ]);

        $vale = Valesgerenciale::find($datos['id']);
        $vale->estatus = 3;
        $vale->save();
        $vale->calendario = Calendario::find($vale->idCalendario)->nombre;
        $vale->bg = 'bg-amarillo';
        return $vale;
      } catch (Exception $e) {
        return null;
      }
    }
  }

?>