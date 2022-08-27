<?php

namespace App\Http\Controllers\Auth\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;

class ResetPasswordController extends Controller
{
    use ApiResponser;
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;

     protected function sendResetResponse(Request $request, $response)
    {
        return $this->successResponse('Su contraseña se ha actualizado con éxito',200);
    }

     protected function sendResetFailedResponse(Request $request, $response)
    {
        return $this->errorResponse($response,422);
    }

  
}