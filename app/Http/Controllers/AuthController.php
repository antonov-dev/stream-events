<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Logout user
     * @return void
     */
    public function logout()
    {
        Auth::guard('web')->logout();
    }
}
