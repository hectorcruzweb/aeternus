<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Documentos extends Model
{
     protected $table = 'documentos';

     public function areas()
    {
        return $this->hasMany('App\AreasFirmas', 'documentos_id', 'id');
    }
}
