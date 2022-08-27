<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticulosRetenciones extends Model
{
    protected $table = 'articulos_retenciones';    
    
    public function satImpuesto() {
        return $this->hasOne('App\SATImpuestos', 'id', 'sat_impuestos_id');
    }
}
