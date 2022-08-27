<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class VentaDetalle extends Model
{
    protected $table = 'venta_detalle';


    public function articulo()
    {
       return $this->hasOne('App\Articulos','id','articulos_id');
    }
}
