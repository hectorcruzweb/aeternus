<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class RegistrosChecador extends Model
{
    protected $table = 'registros_checador';

    public function usuario()
    {
        return $this->belongsTo('App\User', 'usuarios_id', 'id');
    }
}
