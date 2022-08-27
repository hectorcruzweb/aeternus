<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SATMonedas;
class SATMonedasController extends ApiController
{
    public function getAll() {
        $monedas = SATMonedas::select('id as value', 'descripcion as label')->get();
        return $this->showAll($monedas);
    }
}
