<?php

namespace App;
use App\Usuario;
use App\Formaspago;
use App\Empleado;

use Illuminate\Database\Eloquent\Model;

class Creditoabono extends Model
{
    protected $table = 'creditoabonos';

    protected $primaryKey = 'id';

    public $incrementing = true;

    protected $keyType = 'int';

    public $timestamps = true;

    protected $fillable = [
        'idCredito',
        'idUsuario',
        'idFormaPago',
        'idCuenta',
        'monto',
        'tipo',
        'idIngreso',
        'idEgreso',
        'activo',
        'eliminado',
    ];

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0,
        'tipo' => 1
    ];

    protected $appends = [
	    'empleado',
	    'forma',
	    'tipoNombre',
	    'ingreso',
	    'egreso',
	    'cuenta'
	];

	

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

	public function RelIngreso()
    {
        return $this->belongsTo(Ingreso::class, 'idIngreso');
    }

    public function RelEgreso()
    {
        return $this->belongsTo(Egreso::class, 'idIngreso');
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

	public function getTipoNombreAttribute()
	{
	    return $this->tipo == 1
	        ? 'Capital'
	        : 'Impuestos';
	}

	public function getIngresoAttribute()
    {
        return optional($this->RelIngreso)->folio;
    }

    public function getEgresoAttribute()
    {
        return optional($this->RelEgreso)->folio;
    }

    public function getCuentaAttribute()
	{
	    return optional($this->RelCuenta)->nombre ?: 'Efectivo';
	}
}
