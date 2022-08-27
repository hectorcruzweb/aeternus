<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Localidades;

class LocalidadesController extends Controller
{
    public function getLocalidades($municipioId) {
        $localidades = Localidades::select('id as value', 'nombre as label')->where('municipio_id', $municipioId)->get()->toJson(JSON_PRETTY_PRINT);
        return response($localidades, 200);
    }
}
