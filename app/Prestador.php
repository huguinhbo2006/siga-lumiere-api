<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Prestador extends Model
{
    // Nombre de la tabla SIEMPRE en minúsculas
    protected $table = 'prestadores';

    // Llave primaria
    protected $primaryKey = 'id';

    // Auto-incremento (true por defecto)
    public $incrementing = true;

    // Tipos de la llave primaria
    protected $keyType = 'int';

    // Timestamps activados
    public $timestamps = true;

    protected $fillable = [
        'nombre',
        'activo',
        'eliminado',
    ];

    protected $attributes = [
        'activo' => 1,
        'eliminado' => 0
    ];
}
