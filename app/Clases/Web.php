<?php  

  	namespace App\Clases;
  	use App\Webpagina;
	use App\Webmenu;
	use App\Websubmenu;
	use App\Webcomponente;
	use App\Webpaginaconfiguracione;
	use App\Webbannerconfiguracione;
	use App\Webtituloconfiguracione;
	use App\Webvideoconfiguracione;
	use App\Webcursoconfiguracione;
	use App\Webcursobeneficio;
	use App\Webcursoextra;
	use App\Webaltasconfiguracione;
	use App\Webvigencia;
	use App\Webtestimoniosconfiguracione;

  	class Web{

  		function paginas(){
  			try {
  				return Webpagina::all();
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function configuraciones($paginaID){
  			try {
  				return Webpaginaconfiguracione::join('webcomponentes', 'idComponente', '=', 'webcomponentes.id')->
                select('webpaginaconfiguraciones.*', 'webcomponentes.nombre as componente', 'webcomponentes.configuracion as configuracion')->
                where('idPagina', '=', $paginaID)->orderBy('posicion', 'ASC')->get();
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function totalConfiguraciones($paginaID){
  			try {
  				return Webpaginaconfiguracione::where('idPagina', '=', $paginaID)->count();
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function eliminarConfiguracion($configuracionID){
  			try {
  				$configuracion = Webpaginaconfiguracione::find($configuracionID);
  				if(intval($configuracion->idComponente) === 1)
  					$this->eliminarBanner($configuracion->idComponente);
	            if(intval($configuracion->idComponente) === 2)
	            	$this->eliminarTitulo($configuracion->idComponente);
	            if(intval($configuracion->idComponente) === 8)
	            	$this->eliminarTestimonios($configuracion->idComponente);
	            if(intval($configuracion->idComponente) === 9)
	            	$this->eliminarAltas($configuracion->idComponente);
	            if(intval($configuracion->idComponente) === 10)
	            	$this->eliminarVideo($configuracion->idComponente);

	            $configuracion->delete();
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function nuevaConfiguracion($paginaID, $componenteID){
  			try {
  				return Webpaginaconfiguracione::create([
		            'idPagina' => $paginaID,
		            'idComponente' => $componenteID,
		            'posicion' => ($this->totalConfiguraciones($paginaID)+1),
		            'eliminado' => 0,
		            'activo' => 1
		        ]);
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function listas(){
  			try {
  				return array(
  					'componentes' => Webcomponente::where('eliminado', '=', 0)->get(),
  					'paginas' => Webpagina::all()
  				);
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function eliminarBanner($configuracionID){
  			try {
  				return Webbannerconfiguracione::where('idConfiguracion', '=', $configuracionID)->orderBy('posicion', 'ASC')->delete();
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function eliminarTitulo($configuracionID){
  			try {
  				return Webtituloconfiguracione::where('idConfiguracion', '=', $configuracionID)->delete();
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function eliminarTestimonios($configuracionID){
  			try {
  				return Webtestimoniosconfiguracione::where('idConfiguracion', '=', $configuracionID)->delete();
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function eliminarAltas($configuracionID){
  			try {
  				return Webaltasconfiguracione::where('idConfiguracion', '=', $configuracionID)->delete();
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function eliminarVideo($configuracionID){
  			try {
  				return Webvideoconfiguracione::where('idConfiguracion', '=', $configuracionID)->delete();
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  		function organizarConfiguraciones($paginaID){
  			try {
  				$configuraciones = Webpaginaconfiguracione::join('webcomponentes', 'idComponente', '=', 'webcomponentes.id')->
                select('webpaginaconfiguraciones.*', 'webcomponentes.nombre as componente', 'webcomponentes.configuracion as configuracion')->
                where('idPagina', '=', $paginaID)->orderBy('posicion', 'ASC')->get();
	            for ($i=0; $i < count($configuraciones); $i++) { 
	                $configuracion = Webpaginaconfiguracione::find($configuraciones[$i]['id']);
	                $configuracion->posicion = ($i+1);
	                $configuracion->save();
	            }
	            return $configuraciones;
  			} catch (Exception $e) {
  				return null;
  			}
  		}

  	}

?>