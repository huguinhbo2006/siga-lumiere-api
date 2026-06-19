<?php  

	namespace App\Clases;
	use App\Metasme;
	use App\Calendario;
	use App\Sucursale;
	use App\Nivele;
	use App\Subnivele;
	use App\Categoria;
  	class Metasmes{

  		function mostrar(){
  			try {
  				return Metasme::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
	            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
	            join('niveles', 'idNivel', '=', 'niveles.id')->
	            join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
	            select(
	                'metasmes.*', 
	                'calendarios.nombre as calendario',
	                'sucursales.nombre as sucursal',
	                'niveles.nombre as nivel',
	                'subniveles.nombre as subnivel'
	            )->whereRaw('calendarios.fin > NOW()')->get();
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function listas(){
  			try {
  				return array(
  					'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->whereRaw('fin > NOW()')->get(),
  					'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  					'subniveles' => Subnivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  					'sucursales' => Sucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
  				);
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function existe($calendarioID, $nivelID, $subnivelID, $sucursalID, $mes){
  			try {
  				return (Metasme::where('idCalendario', '=', $calendarioID)->
	            where('idSucursal', '=', $sucursalID)->
	            where('idNivel', '=', $nivelID)->
	            where('idSubnivel', '=', $subnivelID)->
	            where('mes', '=', $mes)->count() > 0);
  			} catch (Exception $e) {
  				return false;
  			}
  		}

  		function nuevo($calendarioID, $nivelID, $subnivelID, $sucursalID, $mes, $meta){
  			try {
  				return Metasme::create([
                    'idCalendario' => $calendarioID,
                    'idSucursal' => $sucursalID,
                    'idNivel' => $nivelID,
                    'idSubnivel' => $subnivelID,
                    'mes' => $mes,
                    'meta' => $meta,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function completar($id){
  			try {
  				return Metasme::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
	            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
	            join('niveles', 'idNivel', '=', 'niveles.id')->
	            join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
	            select(
	                'metasmes.*', 
	                'calendarios.nombre as calendario',
	                'sucursales.nombre as sucursal',
	                'niveles.nombre as nivel',
	                'subniveles.nombre as subnivel'
	            )->where('metasmes.id', '=', $id)->get()[0];
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function modificar($id, $meta){
  			try {
  				$dato = Metasme::find($id);
  				$dato->meta = $meta;
  				$dato->save();
  				return $dato;
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function eliminar($id){
  			try {
  				$dato = Metasme::find($id);
  				$dato->delete();
  				return $dato;
  			} catch (Exception $e) {
  				return null;
  			}
  		}
  	}

?>