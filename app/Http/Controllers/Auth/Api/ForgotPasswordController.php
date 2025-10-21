<?php

namespace App\Http\Controllers\Auth\Api;

use App\User;
use Password;
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

    /**
     * Override sending reset link to check user status
     */
    public function sendResetLinkEmail(Request $request)
    {
        // Validate the email field
        $this->validate($request, ['email' => 'required|email']);

        // Find user
        $user = User::where('email', $request->email)->first();

        // Check if user exists and is active (status = 1)
        if (!$user) {
            return $this->errorResponse('Este usuario no existe.', 403);
        } else if ($user->status != 1) {
            return $this->errorResponse('Usuario inhabilitado para iniciar sesión.', 403);
        }

        // Send the reset link
        $response = $this->broker()->sendResetLink(
            $request->only('email')
        );

        // Return appropriate API response
        return $response == Password::RESET_LINK_SENT
            ? $this->sendResetLinkResponse($request, $response)
            : $this->sendResetLinkFailedResponse($request, $response);
    }


    protected function sendResetLinkResponse(Request $request, $response)
    {
        return $this->successResponse('Hemos enviado un enlace a su correo electrónico.', 200);
    }


    protected function sendResetLinkFailedResponse(Request $request, $response)
    {
        return $this->errorResponse($response, 422);
    }
}
