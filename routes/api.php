<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MpesaController;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware(['mpesa.ip'])->group(function () {
    Route::post('/mpesa/validation', [MpesaController::class, 'validation']);
    Route::post('/mpesa/confirmation', [MpesaController::class, 'confirmation']);
    Route::post('/mpesa/callback', [MpesaController::class, 'callback']);
});
