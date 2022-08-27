<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cementerios extends Model
{
    protected $table = 'cementerios';

    public function localidad() {
        return $this->hasOne('App\Localidades', 'id', 'localidades_id');
    }
}
