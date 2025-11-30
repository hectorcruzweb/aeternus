<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\ServiciosFunerariosService;

class DashboardController extends ApiController
{

    protected $serviciosFunerarios;

    public function __construct(
        ServiciosFunerariosService $serviciosFunerarios
    ) {
        $this->serviciosFunerarios = $serviciosFunerarios;
    }

    public function buildDashboard()
    {
        $dashboard = [
            'servicios_funerarios' => $this->serviciosFunerarios->getServicios()
        ];

        return $this->successResponse($dashboard);
    }
}
