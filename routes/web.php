<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\MpesaController;

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

Route::get('/', [HomeController::class, 'index'])->name('home');

// Auth routes
Route::get('/register', [AuthController::class, 'registerView'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware(['auth'])->group(function () {
    // Auction routes
    Route::get('/auction', [AuctionController::class, 'index'])->name('auction');
    Route::get('/auctioncreate', [AuctionController::class, 'create'])->name('auctioncreate');
    Route::post('/auction/store', [AuctionController::class, 'store'])->name('auction.store');
    
    Route::get('/auction/{id}/conditions', [AuctionController::class, 'showConditionsForm'])->name('auction.conditions');
    Route::post('/auction/{id}/conditions', [AuctionController::class, 'storeConditions'])->name('auction.conditions.store');

    // Conditions routes
    Route::get('/auction/{id}/view', [AuctionController::class, 'show'])->name('layouts.condition');
    Route::get('/auction/{id}/conditions', [AuctionController::class, 'conditions'])->name('auction.conditions');
    Route::post('/auction/{id}/conditions', [AuctionController::class, 'storeconditions'])->name('auction.storeconditions');
    
    // Edit and update routes
    Route::get('/auction/{id}/edit', [AuctionController::class, 'edit'])->name('auction.edit');
    Route::put('/auction/{id}/update', [AuctionController::class, 'update'])->name('auction.update');
    
    // Attend route
    Route::post('/auction/{id}/attend', [AuctionController::class, 'attend'])->name('auction.attend');
    
// Add this with your other auction routes
Route::delete('/auction/{id}/destroy', [AuctionController::class, 'destroy'])->name('auction.destroy');

    // Payment routes
    Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/payment/initiate', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/payment/status/{id}', [PaymentController::class, 'showStatus'])->name('payment.status');
});

// Callback routes (no auth required)
Route::post('/payment/callback', [PaymentController::class, 'callback'])->name('payment.callback');
Route::post('/mpesa/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');

// MPesa routes
Route::get('/payment/status', [MpesaController::class, 'showStatus'])->name('mpesa.status');
Route::get('/payment/form', [MpesaController::class, 'showPaymentForm'])->name('mpesa.form');
Route::post('/mpesa/stk-push', [MpesaController::class, 'stkPush'])->name('mpesa.stkpush');