<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialAuthController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::redirect('/', '/spa');

Route::get('/spa/{any?}', function () {
    return view('app', [
        'initialData' => [
            'isAuthenticated' => Auth::check()
        ]
    ]);
})->where('any', '.*');

Route::get('/social-auth/{provider}/redirect', [SocialAuthController::class, 'redirect']);
Route::get('/social-auth/{provider}/callback', [SocialAuthController::class, 'callback']);

Route::middleware('auth:sanctum')->post('/logout', [AuthController::class, 'logout']);

