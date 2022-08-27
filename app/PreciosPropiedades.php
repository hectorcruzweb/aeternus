<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class PreciosPropiedades extends Model
{
    protected $table = 'precios_propiedades';

    //defino el tipo de precio al que pertenece cada precio de la propiedad
    public function tipo()
    {
        return $this->belongsTo('App\TipoPrecios', 'tipo_precios_id', 'id');
    }
}