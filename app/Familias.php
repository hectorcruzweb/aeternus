<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Familias extends Model
{
    protected $table = 'familias';    
    
    public function categoria() {
        return $this->belongsTo('App\Categorias', 'categorias_id', 'id');
    }
}
