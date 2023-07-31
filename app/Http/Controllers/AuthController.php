<?php

namespace App\Http\Controllers;

use App\Jobs\ClearEvents;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Logout user
     * @return void
     */
    public function logout()
    {
        // Clear events for user
        ClearEvents::dispatch(Auth::user());

        Auth::guard('web')->logout();
    }
}
