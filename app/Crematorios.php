<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Crematorios extends Model
{
    protected $table = 'crematorios';

    public function localidad() {
        return $this->hasOne('App\Localidades', 'id', 'localidades_id');
    }
}
