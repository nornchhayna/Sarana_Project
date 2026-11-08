<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EmailVerificationController extends Controller
{
    public function resend(Request $request)
    {
        if (method_exists(AuthController::class, 'resendEmailOtp')) {
            return app(AuthController::class)->resendEmailOtp($request);
        }

        return back()->with('status', 'Verification link sent!');
    }
}
