<?php

namespace App\Http\Controllers;

use App\Services\Dashboard\ServiciosFuneariosService;

class DashboardController extends ApiController
{
    public function servicios_funerarios(ServiciosFuneariosService $service)
    {
        return $this->successResponse(['data' => $service->getServicios()]);
    }
}
