<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Clientes extends Model
{
    protected $table = 'clientes';

    public function nacionalidad()
    {
        return $this->hasOne('App\Nacionalidades', 'id', 'nacionalidades_id');
    }

    public function regimen()
    {
        return $this->hasOne('App\SATRegimenes', 'id', 'regimen_fiscal_id');
    }

    public function genero()
    {
        return $this->hasOne('App\Generos', 'id', 'generos_id');
    }

    public function operaciones()
    {
        return $this->hasMany(Operaciones::class, 'clientes_id')->select('id', 'empresa_operaciones_id', 'clientes_id', 'ventas_terrenos_id', 'servicios_funerarios_id', 'ventas_planes_id', 'ventas_generales_id', 'saldo', 'status', 'fecha_operacion'); // include clientes_id for proper relation
    }
}
