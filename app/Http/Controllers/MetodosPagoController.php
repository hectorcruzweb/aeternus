<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MetodosPago;

class MetodosPagoController extends ApiController
{
    public function __construct() {
        $this->middleware('auth:api');
    }

    public function getAll() {
        $metodos = MetodosPago::get();
        return $this->showAll($metodos);
    }
}
