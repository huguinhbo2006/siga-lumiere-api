<?php

namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;
use App\Ingreso;
use App\Egreso;
use App\Calendario;

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

    protected $appends = [
        'totalIngresos',
        'totalEgresos',
        'totalFinal',
        'positivo'
    ];

    protected $calendarioActual = null;

    public function ingresos()
    {
        return $this->hasMany(Ingreso::class, 'idCuenta', 'id');
    }

    public function egresos()
    {
        return $this->hasMany(Egreso::class, 'idCuenta', 'id');
    }

    private function obtenerCalendario()
    {
        if ($this->calendarioActual === null) {
            $this->calendarioActual = Calendario::where('eliminado', 0)
                ->whereDate('inicio', '<=', date('Y-m-d'))
                ->whereDate('fin', '>=', date('Y-m-d'))
                ->first();
        }

        return $this->calendarioActual;
    }

    public function getTotalIngresosAttribute()
    {
        $calendario = $this->obtenerCalendario();

        if (!$calendario) {
            return 0;
        }

        return (float) Ingreso::where('idCuenta', $this->id)
            ->where('idCalendario', 26)
            ->sum('monto');
    }

    public function getTotalEgresosAttribute()
    {
        $calendario = $this->obtenerCalendario();

        if (!$calendario) {
            return 0;
        }

        return (float) Egreso::where('idCuenta', $this->id)
            ->where('idCalendario', 26)
            ->sum('monto');
    }

    public function getTotalFinalAttribute()
    {
        return round(
            $this->totalIngresos - $this->totalEgresos,
            2
        );
    }

    public function getPositivoAttribute()
    {
        return $this->totalFinal >= 0; 
    }
}