<?php

namespace App;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Model;

class PlanesFunerarios extends Model
{
    protected $table = 'planes_funerarios';

    public function conceptos()
    {
        return $this->hasMany('App\PlanConceptos', 'planes_funerarios_id', 'id')->orderBy('seccion_id', 'asc');
    }


    public function precios()
    {
        return $this->hasMany('App\PreciosPlanes', 'planes_funerarios_id', 'id')
            ->orderBy('id', 'asc');
    }
}