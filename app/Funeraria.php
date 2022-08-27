<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Funeraria extends Model
{
    //
    protected $table = 'funeraria';




    public function regimen()
    {
        return $this->hasOne('App\SATRegimenes', 'id', 'sat_regimenes_id');
    }

    public function registro_publico()
    {
        return $this->belongsTo('App\RegistroPublico', 'id', 'funeraria_id');
    }

    public function cementerio()
    {
        return $this->belongsTo('App\Cementerio', 'id', 'funeraria_id');
    }

    public function facturacion()
    {
        return $this->belongsTo('App\Facturacion', 'id', 'funeraria_id');
    }


    /**de aqui abajo son cambios de andres */
    public function localidad()
    {
        return $this->hasOne('App\Localidades', 'id', 'localidades_id');
    }


    public function terreno()
    {
        return $this->belongsTo('App\VentasTerrenos', 'id', 'ventas_terrenos_id');
    }
}