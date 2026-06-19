<?php  

  namespace App\Clases;
  use App\Metasingreso;
  use App\Ficha;
  use App\Calendario;
  use App\Sucursale;
  use App\Clases\Fichas;
  use Illuminate\Support\Facades\DB;

  class Metasingresos{
    function metasCalendario($calendario){
      try {
        $metas = Metasingreso::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
        join('sucursales', 'idSucursal', '=', 'sucursales.id')->
        select([
            'metasingresos.idMes',
            'metasingresos.idSucursal',
            'metasingresos.idCalendario',
            'metasingresos.meta',
            DB::raw("CONCAT('Meta ',
                            sucursales.nombre,
                            ' ',
                            ELT(metasingresos.idMes, 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'),
                            ' Calendario ',
                            calendarios.nombre
            ) as texto")
        ])->where('idCalendario', '=', $calendario)->
        whereRaw('MONTH(NOW()) = metasingresos.idMes')->get();
        return $metas;
      } catch (Exception $e) {
        return null;
      }
    }

    function fichas($meta){
      try {
        $fichas = Ficha::where('idCalendario', '=', $meta->idCalendario)->
        where('idSucursalInscripcion', '=', $meta->idSucursal)->
        whereRaw('MONTH(DATE_SUB(fichas.created_at, INTERVAL 6 HOUR)) = '.$meta->idMes)->get();

        return $fichas;
      } catch (Exception $e) {
        return null;
      }
    }

    function ventas($metas){
      try {
        $funcionesFichas = new Fichas();
        $final = array();
        foreach ($metas as $meta) {
          $fichas = $this->fichas($meta);
          $ingreso = 0;
          foreach ($fichas as $ficha) {
            $ingreso = $ingreso + $funcionesFichas->costo($ficha->id);
          }
          $final[] = array(
            'meta' => $meta->meta,
            'ingreso' => $ingreso,
            'texto' => $meta->texto
          );
        }
        return $final;
      } catch (Exception $e) {
        return null;
      }
    }

    function mostrar($calendarioID){
      try {
        return Metasingreso::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            select([
                'metasingresos.*',
                DB::raw('ELT(metasingresos.idMes, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") as mes'),
                'sucursales.nombre as sucursal',
                'calendarios.nombre as calendario',
                'metasingresos.idMes as mes'
            ])->where('idCalendario', '=', $calendarioID)->get();
      } catch (Exception $e) {
        return null;
      }
    }

    function listas(){
      try {
        return array(
          'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->whereRaw('fin > NOW()')->get(),
          'sucursales' => Sucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
        );
      } catch (Exception $e) {
        return null;
      }
    }

    function existe($calendarioID, $sucursalID, $mes){
      try {
        return (Metasingreso::where('idCalendario', '=', $calendarioID)->where('idSucursal', '=', $sucursalID)->where('idMes', '=', $mes)->count() > 0);
      } catch (Exception $e) {
        return false;
      }
    }

    function nuevo($calendarioID, $sucursalID, $mes, $meta){
      try {
        return Metasingreso::create([
          'idSucursal' => $sucursalID,
          'idCalendario' => $calendarioID,
          'idMes' => $mes,
          'meta' => $meta,
          'activo' => 1,
          'eliminado' => 0
        ]);
      } catch (Exception $e) {
        return null;
      }
    }

    function completar($id){
      try {
        return Metasingreso::join('calendarios', 'idCalendario', '=', 'calendarios.id')->
        join('sucursales', 'idSucursal', '=', 'sucursales.id')->
        select(
          'metasingresos.*',
          'calendarios.nombre as calendario',
          'sucursales.nombre as sucursal',
          DB::raw('ELT(metasingresos.idMes, "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre") as mes')
        )->where('metasingresos.id', '=', $id)->get()[0];
      } catch (Exception $e) {
        return null;
      }
    }

    function modificar($id, $meta){
      try {
        $dato = Metasingreso::find($id);
        $dato->meta = $meta;
        $dato->save();
        return $dato;
      } catch (Exception $e) {
        return null;
      }
    }

    function eliminar($id){
      try {
        $dato = Metasingreso::find($id);
        $dato->delete();
        return $dato;
      } catch (Exception $e) {
        return null;
      }
    }
  }

?>