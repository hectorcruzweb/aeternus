<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticulosPaquete extends Model
{
    protected $table = 'articulos_paquete';

    public function articulo() {
        return $this->hasOne('App\Articulos', 'id', 'articulos_id')->select(['nombre', 'codigo_barras', 'id']);
    }
}
