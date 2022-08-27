<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\TiposProducto;

class TiposProductoController extends ApiController
{
    public function getAll() {
        $tipos = TiposProducto::select('id as value', 'tipo as label')->get();
        return $this->showAll($tipos);
    }
}
