<?php  
  namespace App\Clases;
  use App\Transferencia;
  use App\Ingreso;
  use App\Egreso;
  use App\Calendario;
  use App\Nivele;
  use App\Sucursale;
  use App\Clases\Folios;
  use Illuminate\Support\Facades\DB;
  use Carbon\Carbon;

  class Transferencias{
    public function __construct(){}
  	
    function recibidas($sucursalID){
      try {
        return Transferencia::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
          join('niveles', 'idNivel', '=', 'niveles.id')->
          where('idSucursalEntrada', '=', $sucursalID)->
          where('transferencias.eliminado', '=', 0)->
          where('transferencias.aceptado', '=', 0)->
          select('transferencias.*', 'calendarios.nombre as calendario', 'niveles.nombre as nivel')->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function creadas($calendarioID){
      try {
        return Transferencia::where('transferencias.idCalendario', '=', $calendarioID)->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function nueva($monto, $calendarioID, $nivelID, $sucursalID, $usuarioID, $sucursalSalidaID){
      try {
        return Transferencia::create([
            'idSucursalSalida' => $sucursalSalidaID,
            'idSucursalEntrada' => $sucursalID,
            'monto' => $monto,
            'aceptado' => 0,
            'idCalendario' => $calendarioID,
            'idUsuarioCreo' => $usuarioID,
            'idNivel' => $nivelID,
            'activo' => 1,
            'eliminado' => 0
        ]);
      } catch (Exception $e) {
        return null;
      }
    }

    function completar($transferencia){
      try {
        $transferencia->sucursal = Sucursale::find($transferencia->idSucursalEntrada)->nombre;
        $transferencia->calendario = Calendario::find($transferencia->idCalendario)->nombre;
        $transferencia->montoFormato = "$".number_format($transferencia->monto, 2, '.', ',');
        $transferencia->fechaFormato = Carbon::parse($transferencia->created_at)->format('d-m-Y h:i:s');
        return $transferencia;
      } catch (Exception $e) {
        return null;
      }
    }

    function listas(){
      try {
        return array(
          'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
          'sucursales' => Sucursale::where('eliminado', '=', 0)->get(),
          'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
        );
      } catch (Exception $e) {
        return null;
      }
    }

    function agregarIngreso($monto, $sucursalID, $usuarioID, $id){
      try {
        $folios = new Folios();
        $transferencia = Transferencia::find($id);
        $folio = $folios->proximoIngreso($transferencia->idNivel, $transferencia->idCalendario, $sucursalID);

        return Ingreso::create([
            'concepto' => 'Transferencia de Corporativo',
            'monto' => $monto,
            'observaciones' => 'Transferencia de efectivo',
            'idRubro' => 2,
            'idTipo' => 4,
            'idSucursal' => $sucursalID,
            'idCalendario' => $transferencia->idCalendario,
            'idFormaPago' => 1,
            'idMetodoPago' => 1,
            'idUsuario' => $usuarioID,
            'idNivel' => $transferencia->idNivel,
            'folio' => $folio,
            'referencia' => 5,
            'activo' => 1,
            'eliminado' => 0,
        ]);
      } catch (Exception $e) {
        return null;
      }
    }

    function agregarEgreso($monto, $sucursalID, $usuarioID, $id){
      try {
        $folios = new Folios();
        $transferencia = Transferencia::find($id);
        $folio = $folios->proximoEgreso($transferencia->idNivel, $transferencia->idCalendario, $sucursalID);

        return Egreso::create([
            'concepto' => 'Transferencia a Sucursal',
            'monto' => $monto,
            'observaciones' => 'Transferencia',
            'idRubro' => 2,
            'idTipo' => 3,
            'idSucursal' => $sucursalID,
            'idCalendario' => $transferencia->idCalendario,
            'idFormaPago' => 1,
            'idUsuario' => $usuarioID,
            'idNivel' => $transferencia->idNivel,
            'folio' => $folio,
            'referencia' => 5,
            'activo' => 1,
            'eliminado' => 0,
        ]);
      } catch (Exception $e) {
        return null;
      }
    }

    function aceptarTransferencia($ingresoID, $egresoID, $usuarioID, $id){
      try {
        $dato = Transferencia::find($id);
        $dato->idUsuarioAcepto = $usuarioID;
        $dato->idIngreso = $ingresoID;
        $dato->aceptado = 1;
        $dato->idEgreso = $egresoID;
        $dato->save();

        return $dato;
      } catch (Exception $e) {
        return null;
      }
    }

    function rechazarTransferencia($id){
      try {
        $dato = Transferencia::find($id);
        $dato->aceptado = 2;
        $dato->save();

        return $dato;
      } catch (Exception $e) {
        return null;
      }
    }
  }

?>