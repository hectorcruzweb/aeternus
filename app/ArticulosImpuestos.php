<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticulosImpuestos extends Model
{
    protected $table = 'articulos_impuestos';

    public function satImpuesto() {
        return $this->hasOne('App\SATImpuestos', 'id', 'sat_impuestos_id');
    }
}
