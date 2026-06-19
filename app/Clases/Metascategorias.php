<?php  

  namespace App\Clases;
  use App\Metascategoria;
  use App\Calendario;
  use App\Sucursale;
  use App\Categoria;

  class Metascategorias{

  	function mostrar(){
  		try {
  			return Metascategoria::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('categorias', 'idCategoria', '=', 'categorias.id')->
            select(
                'metascategorias.*', 
                'calendarios.nombre as calendario',
                'sucursales.nombre as sucursal',
                'categorias.nombre as categoria'
            )->
            where('metascategorias.eliminado', '=', 0)->get();
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function listas(){
  		try {
  			return array(
  				'categorias' => Categoria::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
  				'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->whereRaw('fin > NOW()')->get(),
  				'sucursales' => Sucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get() 
  			);
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function nuevo($calendarioID, $sucursalID, $categoriaID, $meta){
  		try {
  			return Metascategoria::create([
	            'idCalendario' => $calendarioID,
	            'idSucursal' => $sucursalID,
	            'idCategoria' => $categoriaID,
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
  			return Metascategoria::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
            join('categorias', 'idCategoria', '=', 'categorias.id')->
            select(
                'metascategorias.*', 
                'calendarios.nombre as calendario',
                'sucursales.nombre as sucursal',
                'categorias.nombre as categoria'
            )->where('metascategorias.id', '=', $id)->get()[0];
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function existe($calendarioID, $sucursalID, $categoriaID){
  		try {
  			return (Metascategoria::where('idCalendario', '=', $calendarioID)->where('idSucursal', '=', $sucursalID)->where('idCategoria', '=', $categoriaID)->count() > 0);
  		} catch (Exception $e) {
  			return false;
  		}
  	}

  	function modificar($id, $meta){
  		try {
  			$dato = Metascategoria::find($id);
  			$dato->meta = $meta;
  			$dato->save();
  			return $dato;
  		} catch (Exception $e) {
  			return null;
  		}
  	}

  	function eliminar($id){
  		try {
  			$dato = Metascategoria::find($id);
  			$dato->delete();
  			return $dato;
  		} catch (Exception $e) {
  			return null;
  		}
  	}
  }

?>