<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AjusteInventarioDetalle extends Model
{
    protected $table = 'ajuste_detalle';

    public function articulos()
    {
        return $this->belongsTo('App\Articulos', 'articulos_id', 'id')->select('*');
    }
}
