<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class HorariosChecador extends Model
{
    protected $table = 'horarios';

    public function diashorario()
    {
        return $this->hasMany('App\DiasHorariosChecador', 'horarios_id', 'id')
            ->orderBy('semana_numero', 'asc')->orderBy('dia_entrada', 'asc');
    }
}
