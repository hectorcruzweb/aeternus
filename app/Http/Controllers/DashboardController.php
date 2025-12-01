<?php

namespace App\Http\Controllers;

use App\Http\Controllers\FunerariaController;
use App\Http\Controllers\InventarioController;

class DashboardController extends ApiController
{

    protected $serviciosFunerarios;
    protected $inventario;

    public function __construct(
        FunerariaController $serviciosFunerarios,
        InventarioController $inventario
    ) {
        $this->serviciosFunerarios = $serviciosFunerarios;
        $this->inventario = $inventario;
    }

    public function buildDashboard()
    {
        $dashboard = [
            'inventario' => $this->inventario->getInventarioDashboard(),
            'servicios_funerarios' => $this->serviciosFunerarios->getServiciosFunerariosDashboard()
        ];

        return $this->successResponse($dashboard);
    }
}
