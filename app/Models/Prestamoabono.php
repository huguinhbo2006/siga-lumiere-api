<?php

namespace App\Models;

use App\Usuario;
use App\Formaspago;
use App\Empleado;
use App\Cuenta;
use Illuminate\Database\Eloquent\Model;

class Prestamoabono extends Model
{
    // Nombre de la tabla SIEMPRE en minúsculas
    protected $table = 'prestamoabonos';

    // Llave primaria
    protected $primaryKey = 'id';

    // Auto-incremento (true por defecto)
    public $incrementing = true;

    // Tipos de la llave primaria
    protected $keyType = 'int';

    // Timestamps activados
    public $timestamps = true;

    protected $fillable = [
        'idPrestamo',
        'monto',
        'idFormaPago',
        'idUsuario',
        'idCuenta',
        'idCalendario',
        'idEgreso',
        'activo',
        'eliminado',
    ];

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0
    ];

    protected $appends = [
        'empleado',
        'forma',
        'cuenta',
    ];


    public function RelPrestamo()
    {
        return $this->belongsTo(Prestamo::class, 'idPrestamo');
    }

    public function RelCuenta()
    {
        return $this->belongsTo(Cuenta::class, 'idCuenta');
    }

    public function RelUsuario()
    {
        return $this->belongsTo(Usuario::class, 'idUsuario');
    }

    public function RelFormaPago()
    {
        return $this->belongsTo(Formaspago::class, 'idFormaPago');
    }

    public function getFormaAttribute()
    {
        return optional($this->RelFormaPago)->nombre;
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

    public function getCuentaAttribute()
    {
        return optional($this->RelCuenta)->nombre ?: 'Efectivo';
    }
}