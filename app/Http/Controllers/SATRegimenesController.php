<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SATRegimenes;

class SATRegimenesController extends ApiController
{
    public function getAll() {
        $regimenes = SATRegimenes::get();
        return $this->showAll($regimenes);
    }
}
