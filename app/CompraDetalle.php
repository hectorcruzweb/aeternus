<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CompraDetalle extends Model
{
    protected $table = 'compra_detalle';


    public function articulos()
    {
        return $this->belongsTo('App\Articulos', 'articulos_id', 'id')->select('*');
    }
}
