<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use Carbon\Carbon;
use App\Sucursale;
use App\Vale;
use App\Alumnoabono;
use App\Calendario;
use App\Nivele;
use Illuminate\Support\Facades\DB; // <-- Agregado para usar DB::raw

class Ingreso extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    public function getCreatedAtAttribute($value){
        return Carbon::parse($value)->format('d-m-Y');
    }
    
    public function getConceptoAttribute($value){
        return $value;
    }

    protected $fillable = [
        'id', 'concepto', 'monto', 'observaciones', 'idRubro', 'idTipo', 'idSucursal', 'idCalendario', 'idFormaPago', 'idMetodoPago', 'idUsuario', 'activo', 'eliminado', 'created_at', 'update_at', 'referencia', 'folio', 'idNivel', 'imagen', 'idBanco', 'nombreCuenta', 'numeroReferencia', 'idCuenta', 'fecha', 'auditado'
    ];

    protected $hidden = [];

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ingreso) {

            if (is_null($ingreso->activo)) {
                $ingreso->activo = 1;
            }

            if (is_null($ingreso->eliminado)) {
                $ingreso->eliminado = 0;
            }

            if (empty($ingreso->folio)) {

                $cantidad = self::where('idNivel', $ingreso->idNivel)
                    ->where('idCalendario', $ingreso->idCalendario)
                    ->where('idSucursal', $ingreso->idSucursal)
                    ->count();

                $sucursal = Sucursale::find($ingreso->idSucursal);
                $calendario = Calendario::find($ingreso->idCalendario);
                $nivel = Nivele::find($ingreso->idNivel);

                if ($sucursal && $calendario && $nivel) {

                    $separados = explode('-', $calendario->nombre);

                    $ingreso->folio =
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


    public function scopeReporteGeneralCalendario($query, $idCalendario)
    {
        return $query->from('ingresos as i')
            ->join('calendarios as c', 'i.idCalendario', '=', 'c.id')
            ->join('niveles as n', 'i.idNivel', '=', 'n.id')
            ->join('sucursales as s', 'i.idSucursal', '=', 's.id')
            ->join('rubros as ri', 'i.idRubro', '=', 'ri.id')
            ->join('formaspagos as fp', 'i.idFormaPago', '=', 'fp.id')
            ->select(
                'i.id',
                'i.folio',
                'n.nombre as nivel',
                'c.nombre as calendario',
                's.nombre as sucursal',
                DB::raw('MONTHNAME(i.created_at) as mes'),
                DB::raw("DATE_FORMAT(i.created_at, '%Y-%m-%d') as fecha"),
                DB::raw("DATE_FORMAT(i.created_at, '%H:%i:%s') as hora"),
                'ri.nombre as rubro',
                'i.concepto',
                'fp.nombre as forma',
                // Replicamos exactamente tu condición: si es 0 o NULL es Efectivo, si no, busca en cuentas
                DB::raw("
                    IF((i.idCuenta = 0 OR i.idCuenta IS NULL), 
                        'Efectivo', 
                        (SELECT nombre FROM cuentas WHERE id = i.idCuenta)
                    ) as cuenta
                "),
                'i.monto',
                'i.activo'
            )
            ->where('i.idCalendario', $idCalendario);
    }

    public function scopeReporte($query, array $filtros)
    {
        return $query->from('ingresos as i')
            ->join('calendarios as c', 'i.idCalendario', '=', 'c.id')
            ->join('niveles as n', 'i.idNivel', '=', 'n.id')
            ->join('sucursales as s', 'i.idSucursal', '=', 's.id')
            ->join('rubros as ri', 'i.idRubro', '=', 'ri.id')
            ->join('formaspagos as fp', 'i.idFormaPago', '=', 'fp.id')
            ->select(
                'i.id',
                'i.folio',
                'n.nombre as nivel',
                'c.nombre as calendario',
                's.nombre as sucursal',
                DB::raw('MONTHNAME(i.created_at) as mes'),
                DB::raw("DATE_FORMAT(i.created_at, '%Y-%m-%d') as fecha"),
                DB::raw("DATE_FORMAT(i.created_at, '%H:%i:%s') as hora"),
                'ri.nombre as rubro',
                'i.concepto',
                'fp.nombre as forma',
                DB::raw("
                    IF((i.idCuenta = 0 OR i.idCuenta IS NULL), 
                        'Efectivo', 
                        (SELECT nombre FROM cuentas WHERE id = i.idCuenta)
                    ) as cuenta
                "),
                'i.monto',
                'i.activo'
            )
            
            ->when(!empty($filtros['idCalendario']), function ($q) use ($filtros) {
                return $q->where('i.idCalendario', $filtros['idCalendario']);
            })
            ->when(!empty($filtros['idNivel']), function ($q) use ($filtros) {
                return $q->where('i.idNivel', $filtros['idNivel']);
            })
            ->when(!empty($filtros['idSucursal']), function ($q) use ($filtros) {
                return $q->where('i.idSucursal', $filtros['idSucursal']);
            })
            ->when(isset($filtros['activo']), function ($q) use ($filtros) { // Por si quieres filtrar activos/inactivos
                return $q->where('i.activo', $filtros['activo']);
            });
    }
}