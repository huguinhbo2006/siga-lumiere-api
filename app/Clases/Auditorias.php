<?php  

  namespace App\Clases;
  use App\Calendario;
  use App\Sucursale;
  use App\Ingreso;
  use App\Banco;
  use App\Cuenta;
  use App\Auditoria;
  use App\Formaspago;
  use Illuminate\Support\Facades\DB;

  class Auditorias{

  	function listas(){
  		try {
	  		return array(
	  			'calendarios' => Calendario::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
	  			'sucursales' => Sucursale::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
                'cuentas' => Cuenta::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
                'bancos' => Banco::where('eliminado', '=', 0)->where('activo', '=', 1)->get(),
                'formas' => Formaspago::where('eliminado', '=', 0)->where('activo', '=', 1)->get()
	  		);
  		} catch (Exception $e) {                                     
  			return null;
  		}
  	}

  	function buscarIngresos($datos){
  		try {
  			$busqueda = Ingreso::leftjoin('alumnoabonos', 'ingresos.id', '=', 'alumnoabonos.idIngreso')->
            leftjoin('fichas', 'alumnoabonos.idFicha', '=', 'fichas.id')->
            join('formaspagos', 'ingresos.idFormaPago', '=', 'formaspagos.id')->
            leftjoin('auditorias', 'ingresos.id', '=', 'auditorias.idIngreso')->
            select(
                'ingresos.id as id',
                'ingresos.folio as folio',
                'ingresos.fecha as fecha',
                'ingresos.created_at as fecha1',
                'formaspagos.nombre as forma',
                DB::raw("IF(ingresos.idFormaPago <> 1, IF(LENGTH(ingresos.imagen) > 0, 'fas fa-check text-success', 'fas fa-times text-danger'), 'N/A') as voucher"),
                'ingresos.monto',
                'fichas.folio as ficha',
                DB::raw("IF(ingresos.activo = 0, 'fas fa-times text-danger', 'fas fa-check text-success') as activo"),
                'ingresos.auditado as auditado',
                'ingresos.idBanco',
                'ingresos.idFormaPago',
                'ingresos.idCuenta',
                'auditorias.observaciones as observaciones                                                                                                                                                         '
            )->where('ingresos.idCalendario', '=', $datos['idCalendario']);

            ($datos['idSucursal'] !== 0) ? $busqueda->where('ingresos.idSucursal', '=', $datos['idSucursal']) : null;
            ($datos['idCuenta'] !== 0) ? $busqueda->where('ingresos.idCuenta', '=', $datos['idCuenta']) : null;
            $ingresos = $busqueda->get();
            ($datos['idFormaPago'] !== 0) ? $busqueda->where('ingresos.idFormaPago', '=', $datos['idFormaPago']) : null;
            $ingresos = $busqueda->get();

            foreach ($ingresos as $ingreso) {
                switch(intval($ingreso->auditado)){
                    case '2':
                        $ingreso->bg = '';
                        break;
                    case '1':
                        $ingreso->bg = 'bg-verde';
                        break;
                    case '3':
                        $ingreso->bg = 'bg-rojo';
                        break;
                }
                if(intval($ingreso->idFormaPago) !== 1){
                    $ingreso->banco = Banco::find($ingreso->idBanco)->nombre;
                    $ingreso->cuenta = Cuenta::find($ingreso->idCuenta)->nombre;
                }else{
                    $ingreso->banco = 'N/A';
                    $ingreso->cuenta = 'N/A';
                }
                if(is_null($ingreso->ficha)){
                    $ingreso->ficha = "-";
                }
                if(is_null($ingreso->fecha)){
                    $ingreso->fecha = $ingreso->fecha1;
                }
            }
            return $ingresos;
  		} catch (Exception $e) {
  			return null;
  		}
  	}

    function auditarIngreso($id){
        try {
            $ingreso = Ingreso::find($id);
            $ingreso->auditado = 1;
            $ingreso->save();
            return $ingreso;
        } catch (Exception $e) {
            return null;
        }
    }

    function desauditarIngreso($id){
        try {
            $ingreso = Ingreso::find($id);
            $ingreso->auditado = 2;
            $ingreso->save();
            return $ingreso;
        } catch (Exception $e) {
            return null;
        }
    }

    function problemaIngreso($id){
        try {
            $ingreso = Ingreso::find($id);
            $ingreso->auditado = 3;
            $ingreso->save();
            return $ingreso;
        } catch (Exception $e) {
            return null;
        }
    }

    function actualizarFinancierosIngreso($datos){
        try {
            $ingreso = Ingreso::find($datos['id']);
            $ingreso->idCuenta = $datos['idCuenta'];
            $ingreso->idBanco = $datos['idBanco'];
            $ingreso->save();
            return $ingreso;
        } catch (Exception $e) {
            return null;
        }
    }

    function observacionesIngreso($datos){
        try {
            $existe = Auditoria::where('idIngreso', '=', $datos['id'])->get();
            $ingreso = array();
            if(count($existe) > 0){
                $ingreso = $existe[0];
                $ingreso->observaciones = $datos['observaciones'];
                $ingreso->save();
            }else{
                $ingreso = Auditoria::create([
                    'idIngreso' => $datos['id'],
                    'observaciones' => $datos['observaciones'],
                    'idUsuario' => $datos['usuarioID']
                ]);
            }
            
            return $ingreso;
        } catch (Exception $e) {
            return null;
        }
    }

    function voucherIngreso($id){
        try {
            return Ingreso::find($id)->imagen;
        } catch (Exception $e) {
            return null;
        }
    }

    function posibles_ingresos($bancoID){
        try {
            return Ingreso::select(
                'ingresos.id',
                'ingresos.folio',
                'ingresos.monto',
                'ingresos.numeroReferencia'
            )->
            where('idFormaPago', '<>', 1)->
            where('auditado', '=', 0)->get();
        } catch (Exception $e) {
            return null;
        }
    }
  }

?>