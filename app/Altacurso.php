<?php
namespace App;

use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Model;
use Laravel\Lumen\Auth\Authorizable;

class Altacurso extends Model implements AuthenticatableContract, AuthorizableContract
{
    use Authenticatable, Authorizable;

    protected $fillable = [
        'updated_at', 'created_at', 'id', 'precio', 'inicio', 'fin', 'idNivel', 'idSubnivel', 'idCurso', 'idCalendario', 'idModalidad', 'eliminado', 'activo', 'idCategoria', 'limitePago', 'idSede'
    ];

    protected $hidden = [];

    
}
