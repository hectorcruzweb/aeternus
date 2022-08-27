<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Categorias;
use App\Familias;

class CategoriasController extends ApiController
{
    public function getAll() {
        $categorias = Categorias::select('id as value', 'categoria as label')->get();
        return $this->showAll($categorias);
    }

    public function getFamilias($idCategoria) {
        $familias = Familias::where('categorias_id', $idCategoria)->select('id as value', 'familia as label')->get();
        return $this->showAll($familias);
    }
}
