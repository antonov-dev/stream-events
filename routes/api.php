<?php

use App\Http\Controllers\Api\EventsController;
use App\Http\Controllers\Api\EventStatsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/events', [EventsController::class, 'index']);
Route::middleware('auth:sanctum')->patch('/events/{event}', [EventsController::class, 'update']);

Route::middleware('auth:sanctum')->get('/event-stats', [EventStatsController::class, 'index']);
