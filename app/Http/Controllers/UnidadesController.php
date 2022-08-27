<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Unidades;

class UnidadesController extends ApiController
{    
    public function getAll() {
        $unidades = Unidades::select('id as value', 'unidad as label')->get();
        return $this->showAll($unidades);
    }
}
