<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Roles extends Model
{
    protected $table = 'roles';


    public function permisos()
    {
        return $this->hasMany('App\RolesPermisos', 'roles_id', 'id');
    }
}