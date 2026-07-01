<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use App\Sucursale;
use App\Calendario;
use App\Nivele;
use Illuminate\Support\Facades\DB;

class Egreso extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'id',
        'concepto',
        'monto',
        'observaciones',
        'idRubro',
        'idTipo',
        'idSucursal',
        'idCalendario',
        'idFormaPago',
        'idUsuario',
        'activo',
        'eliminado',
        'created_at',
        'update_at',
        'referencia',
        'idNivel',
        'folio',
        'idCuenta',
        'voucher',
        'idSucursalGasto'
    ];

    protected $hidden = [];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($egreso) {

            if (empty($egreso->folio)) {

                $cantidad = self::where('idNivel', $egreso->idNivel)
                    ->where('idCalendario', $egreso->idCalendario)
                    ->where('idSucursal', $egreso->idSucursal)
                    ->count();

                $sucursal = Sucursale::find($egreso->idSucursal);
                $calendario = Calendario::find($egreso->idCalendario);
                $nivel = Nivele::find($egreso->idNivel);

                if ($sucursal && $calendario && $nivel) {

                    $separados = explode('-', $calendario->nombre);

                    $egreso->folio =
                        substr($separados[0], -2) .
                        $separados[1] .
                        substr($nivel->nombre, 0, 1) .
                        $sucursal->abreviatura .
                        '-' .
                        ($cantidad + 1);
                }
            }
        });
    }

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0
    ];

    //Funciones Scope para reportes

    /**
     * Scope escalable para reportes de egresos con filtros dinámicos
     */
    public function scopeReporte($query, array $filtros)
    {
        return $query->from('egresos as e')
            ->join('calendarios as c', 'e.idCalendario', '=', 'c.id')
            ->join('niveles as n', 'e.idNivel', '=', 'n.id')
            ->join('sucursales as s', 'e.idSucursal', '=', 's.id')
            ->leftJoin('sucursales as sg', 'e.idSucursalGasto', '=', 'sg.id')
            ->join('rubrosegresos as re', 'e.idRubro', '=', 're.id')
            ->join('tiposegresos as te', 'e.idTipo', '=', 'te.id')
            ->join('formaspagos as fp', 'e.idFormaPago', '=', 'fp.id')
            ->select(
                'e.id',
                'e.folio',
                'n.nombre as nivel',
                'c.nombre as calendario',
                's.nombre as sucursalCaptura',
                DB::raw("COALESCE(sg.nombre, 'NA') as sucursalEgreso"),
                DB::raw('MONTHNAME(e.created_at) as mes'),
                DB::raw("DATE_FORMAT(e.created_at, '%Y-%m-%d') as fecha"),
                DB::raw("DATE_FORMAT(e.created_at, '%H:%i:%s') as hora"),
                're.nombre as rubro',
                'te.nombre as tipo',
                'e.concepto',
                'fp.nombre as forma',
                DB::raw("
                    IF((e.idCuenta = 0 OR e.idCuenta IS NULL), 
                        'Efectivo', 
                        (SELECT nombre FROM cuentas WHERE id = e.idCuenta)
                    ) as cuenta
                "),
                'e.monto',
                'e.activo'
            )
            /* =======================================================
               BLOQUE DE FILTROS DINÁMICOS PARA EGRESOS
               ======================================================= */
            ->when(!empty($filtros['idCalendario']), function ($q) use ($filtros) {
                return $q->where('e.idCalendario', $filtros['idCalendario']);
            })
            ->when(!empty($filtros['idNivel']), function ($q) use ($filtros) {
                return $q->where('e.idNivel', $filtros['idNivel']);
            })
            ->when(!empty($filtros['idSucursal']), function ($q) use ($filtros) {
                return $q->where('e.idSucursal', $filtros['idSucursal']);
            })
            ->when(!empty($filtros['idSucursalGasto']), function ($q) use ($filtros) {
                return $q->where('e.idSucursalGasto', $filtros['idSucursalGasto']);
            })
            ->when(isset($filtros['activo']), function ($q) use ($filtros) {
                return $q->where('e.activo', $filtros['activo']);
            });
    }
}