<?php

namespace App\Http\Controllers;
use App\Clases\Auditorias;
use Laravel\Lumen\Routing\Controller as BaseController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Ingreso;
use App\Alumnoabono;
use App\Ficha;
use App\Alumno;

class AuditoriasController extends BaseController
{
    function listas(){
        try {
            $funciones = new Auditorias();
            return response()->json($funciones->listas(), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function ingresos(Request $request){
        try {
            $funciones = new Auditorias();
            return response()->json($funciones->buscarIngresos($request), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function auditarIngreso(Request $request){
        try {
            $funciones = new Auditorias();
            return response()->json($funciones->auditarIngreso($request['id']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function desauditarIngreso(Request $request){
        try {
            $funciones = new Auditorias();
            return response()->json($funciones->desauditarIngreso($request['id']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function problemaIngreso(Request $request){
        try {
            $funciones = new Auditorias();
            return response()->json($funciones->problemaIngreso($request['id']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function financierosIngreso(Request $request){
        try {
            $funciones = new Auditorias();
            return response()->json($funciones->actualizarFinancierosIngreso($request), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    function observacionesIngreso(Request $request){
        try {
            $funciones = new Auditorias();
            return response()->json($funciones->observacionesIngreso($request), 200);
        } catch (Exception $e) {
            return null;
        }
    }

    function voucherIngreso(Request $request){
        try {
            $funciones = new Auditorias();
            return response()->json($funciones->voucherIngreso($request['id']), 200);
        } catch (Exception $e) {
            return response()->json('Error en el servidor', 400);
        }
    }

    public function posiblesIngresos(Request $request){
        try {

            $ingresos = Ingreso::select(
                    'ingresos.id',
                    'ingresos.folio',
                    'ingresos.monto',
                    'ingresos.numeroReferencia',
                    'ingresos.fecha',
                    'ingresos.concepto'
                )
                ->where('idFormaPago', '<>', 1)
                ->where('auditado', 0)
                ->where('idCuenta', $request['idCuenta'])
                ->whereYear('fecha', $request['anio'])
                ->whereMonth('fecha', $request['mes'])
                ->get();


            foreach ($ingresos as $ingreso) {
                $abono = Alumnoabono::where('idIngreso', $ingreso->id)->first();
                $ficha = Ficha::find($abono->idFicha);
                $alumno = Alumno::select(
                    DB::raw("CONCAT(nombre, ' ', apellidoPaterno, ' ', apellidoMaterno) as alumno")
                )->find($ficha->idAlumno);
                $ingreso->concepto = $alumno->alumno;
            }
            return response()->json($ingresos, 200);

        } catch (Exception $e) {

            return response()->json('Error en el servidor', 400);

        }
    }
}