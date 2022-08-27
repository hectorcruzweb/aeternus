<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Estados;

class EstadosController extends Controller
{
    public function getEstados() {
        $estados = Estados::select('id as value','nombre as label')->get()->toJson(JSON_PRETTY_PRINT);
        return response($estados, 200);
    }
}
