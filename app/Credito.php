<?php

namespace App;

use App\Prestador;
use App\Calendario;
use App\Nivele;
use App\Usuario;
use App\Empleado;
use App\Ingreso;
use App\Sucursale;
use App\Formaspago;
use App\Cuenta;
use App\Creditoabono;
use App\Egreso;
use Illuminate\Database\Eloquent\Model;

class Credito extends Model
{
    protected $table = 'creditos';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'idPrestador',
        'idCuenta',
        'idFormaPago',
        'idUsuario',
        'idSucursal',
        'idIngreso',
        'idEgreso',
        'idNivel',
        'idCalendario',
        'monto',
        'observaciones',
        'activo',
        'eliminado',
        'tipo'
    ];

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0
    ];

    protected $appends = [
        'prestador',
        'cuenta',
        'forma',
        'empleado',
        'sucursal',
        'ingreso',
        'egreso',
        'nivel',
        'calendario',
        'abonoCapital',
        'abonoImpuestos',
        'estatus',
        'bg',
        'abonos'
    ];

    public function RelAbonos()
    {
        return $this->hasMany(Creditoabono::class, 'idCredito');
    }

    public function RelPrestador()
    {
        return $this->belongsTo(Prestador::class, 'idPrestador');
    }

    public function RelCuenta()
    {
        return $this->belongsTo(Cuenta::class, 'idCuenta');
    }

    public function RelFormaPago()
    {
        return $this->belongsTo(Formaspago::class, 'idFormaPago');
    }

    public function RelSucursal()
    {
        return $this->belongsTo(Sucursale::class, 'idSucursal');
    }

    public function RelIngreso()
    {
        return $this->belongsTo(Ingreso::class, 'idIngreso');
    }

    public function RelEgreso()
    {
        return $this->belongsTo(Egreso::class, 'idIngreso');
    }

    public function RelUsuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function RelCalendario()
    {
        return $this->belongsTo(Calendario::class, 'idCalendario');
    }

    public function RelNivel()
    {
        return $this->belongsTo(Nivele::class, 'idNivel');
    }

    public function getPrestadorAttribute()
    {
        if ($this->tipo == 1) {
            return optional($this->RelPrestador)->nombre;
        }

        if ($this->tipo == 2) {
            return optional(Calendario::find($this->idPrestador))->nombre;
        }

        return null;
    }

    public function getCuentaAttribute()
	{
	    return optional($this->RelCuenta)->nombre ?: 'Efectivo';
	}

    public function getFormaAttribute()
    {
        return optional($this->RelFormaPago)->nombre;
    }

    public function getSucursalAttribute()
    {
        return optional($this->RelSucursal)->nombre;
    }

    public function getNivelAttribute()
    {
        return optional($this->RelNivel)->nombre;
    }

    public function getCalendarioAttribute()
    {
        return optional($this->RelCalendario)->nombre;
    }

    public function getIngresoAttribute()
    {
        return optional($this->RelIngreso)->folio;
    }

    public function getEgresoAttribute()
    {
        return optional($this->RelEgreso)->folio;
    }

    public function getEmpleadoAttribute()
    {
        $usuario = $this->RelUsuario;

        if (!$usuario) {
            return null;
        }

        $empleado = Empleado::find($usuario->idEmpleado);

        return $empleado ? $empleado->nombre : null;
    }

    public function getAbonoCapitalAttribute()
    {
        return $this->RelAbonos()
            ->where('tipo', 1)
            ->where('eliminado', 0)
            ->sum('monto');
    }

    public function getAbonoImpuestosAttribute()
    {
        return $this->RelAbonos()
            ->where('tipo', 2)
            ->where('eliminado', 0)
            ->sum('monto');
    }

    public function getEstatusAttribute()
    {
        return $this->abonoCapital >= $this->monto
            ? 'PAGADO'
            : 'ACTIVO';
    }

    public function getBgAttribute()
    {
        return $this->abonoCapital >= $this->monto
            ? 'bg-verde'
            : 'bg-rojo';
    }

    public function getAbonosAttribute()
    {
        return $this->RelAbonos()
            ->where('eliminado', 0)
            ->orderBy('created_at', 'desc')
            ->get();
    }
}