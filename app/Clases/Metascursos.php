<?php  

  	namespace App\Clases;
  	use App\Altacurso;
	use App\Nivele;
	use App\Subnivele;
	use App\Curso;
	use App\Modalidade;
	use App\Calendario;
	use App\Categoria;
	use App\Sede;
	use App\Metascurso;
	use App\Sucursale;
  	class Metascursos{

	  	function mostrar(){
	  		try {
	  			return Metascurso::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
	            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
	            join('niveles', 'idNivel', '=', 'niveles.id')->
	            join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
	            join('modalidades', 'idModalidad', '=', 'modalidades.id')->
	            join('cursos', 'idCurso', '=', 'cursos.id')->
	            select(
	                'metascursos.*', 
	                'calendarios.nombre as calendario',
	                'sucursales.nombre as sucursal',
	                'niveles.nombre as nivel',
	                'subniveles.nombre as subnivel',
	                'modalidades.nombre as modalidad',
	                'cursos.nombre as curso'
	            )->whereRaw('calendarios.fin > NOW()')->get();
	  		} catch (Exception $e) {
	  			return null;
	  		}
	  	}

	  	function listas(){
	  		try {
	  			return array(
	  				'altas' => Altacurso::whereRaw("DATE_FORMAT(fin,'%y-%m-%d') > CURDATE()")->get(),
	  				'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->whereRaw('fin > NOW()')->get(),
	  				'niveles' => Nivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  				'subniveles' => Subnivele::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  				'modalidades' => Modalidade::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  				'cursos' => Curso::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  				'sucursales' => Sucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get() 
	  			);
	  		} catch (Exception $e) {
	  			return null;
	  		}
	  	}

	  	function nuevo($calendarioID, $nivelID, $subnivelID, $modalidadID, $cursoID, $sucursalID, $meta){
	  		try {
	  			return Metascurso::create([
                    'idCalendario' => $calendarioID,
                    'idNivel' => $nivelID,
                    'idSubnivel' => $subnivelID,
                    'idModalidad' => $modalidadID,
                    'idCurso' => $cursoID,
                    'idSucursal' => $sucursalID,
                    'meta' => $meta,
                    'eliminado' => 0,
                    'activo' => 1
                ]);
	  		} catch (Exception $e) {
	  			return null;
	  		}
	  	}

	  	function existe($calendarioID, $nivelID, $subnivelID, $modalidadID, $cursoID, $sucursalID){
	  		try {
	  			return (Metascurso::where('idCalendario', '=', $calendarioID)->
		        where('idNivel', '=', $nivelID)->
		        where('idSubnivel', '=', $subnivelID)->
		        where('idModalidad', '=', $modalidadID)->
		        where('idCurso', '=', $cursoID)->
		        where('idSucursal', '=', $sucursalID)->count() > 0);	
	  		} catch (Exception $e) {
	  			return false;
	  		}
	  	}

	  	function completar($id){
	  		try {
	  			return Metascurso::join('sucursales', 'idSucursal', '=', 'sucursales.id')->
	            join('calendarios', 'idCalendario', '=', 'calendarios.id')->
	            join('niveles', 'idNivel', '=', 'niveles.id')->
	            join('subniveles', 'idSubnivel', '=', 'subniveles.id')->
	            join('modalidades', 'idModalidad', '=', 'modalidades.id')->
	            join('cursos', 'idCurso', '=', 'cursos.id')->
	            select(
	                'metascursos.*', 
	                'calendarios.nombre as calendario',
	                'sucursales.nombre as sucursal',
	                'niveles.nombre as nivel',
	                'subniveles.nombre as subnivel',
	                'modalidades.nombre as modalidad',
	                'cursos.nombre as curso'
	            )->where('metascursos.id', '=', $id)->get()[0];
	  		} catch (Exception $e) {
	  			return null;
	  		}
	  	}

	  	function modificar($id, $meta){
	  		try {
	  			$dato = Metascurso::find($id);
	  			$dato->meta = $meta;
	  			$dato->save();
	  			return $dato;
	  		} catch (Exception $e) {
	  			return null;
	  		}
	  	}

	  	function eliminar($id){
	  		try {
	  			$meta =Metascurso::find($id);
	  			$meta->delete();
	  			return $meta;
	  		} catch (Exception $e) {
	  			return null;
	  		}
	  	}
  	}

?>