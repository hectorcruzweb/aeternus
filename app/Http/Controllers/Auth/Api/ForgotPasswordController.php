<?php

namespace App\Http\Controllers\Auth\Api;

use App\Traits\ApiResponser;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;

class ForgotPasswordController extends Controller
{

    use ApiResponser;
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset emails and
    | includes a trait which assists in sending these notifications from
    | your application to your users. Feel free to explore this trait.
    |
    */

    use SendsPasswordResetEmails;


     protected function sendResetLinkResponse(Request $request, $response)
    {
        return $this->successResponse('Hemos enviado un enlace a su correo electrónico.',200);
    }


    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return $this->errorResponse($response,422);
    }

}