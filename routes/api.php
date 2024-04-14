<?php

use Illuminate\Support\Facades\Route;
use NormanHuth\Library\Http\Controllers\Api\SentryTunnelController;

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

Route::get('/', fn () => response()->json(['message' => 'It works!']));
Route::post('sentry-tunnel', SentryTunnelController::class);
