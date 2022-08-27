<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AreasFirmas extends Model
{
    protected $table = 'areas_firmas';

    public function firma() {
        return $this->hasOne('App\Firmas', 'id', 'areas_firmas_id');
    }
}
