<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Municipios;

class MunicipiosController extends Controller
{
    public function getMunicipios($estadoId) {
        $municipios = Municipios::select('id as value', 'nombre as label')->where('estado_id', $estadoId)->get()->toJson(JSON_PRETTY_PRINT);
        return response($municipios, 200);
    }
}
