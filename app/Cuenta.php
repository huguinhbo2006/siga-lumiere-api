<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use App\Cuentacorte;
use App\Ingreso;
use App\Egreso;

class Cuenta extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $table = 'cuentas';

    protected $fillable = [
        'nombre',
        'activo',
        'eliminado'
    ];

    protected $hidden = [];

    /**
     * Campos calculados que se agregan al JSON
     */
    protected $appends = [
        'totalInicial',
        'totalIngresos',
        'totalEgresos',
        'totalFinal',
    ];

    /* =========================
       RELACIONES
       ========================= */

    public function ingresos()
    {
        return $this->hasMany(Ingreso::class, 'idCuenta', 'id');
    }

    public function egresos()
    {
        return $this->hasMany(Egreso::class, 'idCuenta', 'id');
    }

    private function obtenerUltimoCorte()
    {
        return Cuentacorte::where('idCuenta', $this->id)
            ->where('activo', 1)
            ->where('eliminado', 0)
            ->orderByDesc('fecha')
            ->first();
    }

    /* =========================
       ACCESSORS
       ========================= */

    public function getTotalInicialAttribute()
    {
        $corte = $this->obtenerUltimoCorte();

        return $corte ? (float) $corte->monto : 0;
    }

    public function getTotalIngresosAttribute()
    {
        $corte = $this->obtenerUltimoCorte();

        $query = Ingreso::where('idCuenta', $this->id);

        if ($corte) {
            $query->whereDate('fecha', '>', $corte->fecha);
        }

        return (float) $query->sum('monto');
    }

    public function getTotalEgresosAttribute()
    {
        $corte = $this->obtenerUltimoCorte();

        $query = Egreso::where('idCuenta', $this->id);

        if ($corte) {
            $query->whereDate('created_at', '>', $corte->fecha);
        }

        return (float) $query->sum('monto');
    }

    public function getTotalFinalAttribute()
    {
        return
            $this->totalInicial +
            $this->totalIngresos -
            $this->totalEgresos;
    }
}
