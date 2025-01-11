<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuctionController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PaymentController;

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

Route::get('/', function () {
    return view('welcome');
});


Route::get('/auction', [AuctionController::class, 'index'])->name('auction');

Route::get('/auctioncreate', [AuctionController::class, 'auctioncreate'])->name('auctioncreate');

Route::post('/auction/store', [AuctionController::class, 'store'])->name('auction.store');

Route::get('/auction/{id}/conditions', [AuctionController::class, 'storeconditions'])->name('layouts.condition');

Route::get('/auction/{id}/conditions', [AuctionController::class, 'conditions'])->name('layouts.condition');
Route::post('/auction/{id}/conditions', [AuctionController::class, 'storeconditions'])->name('auction.storeconditions');

Route::post('/auction/{id}/attend', [AuctionController::class, 'attend'])->name('auction.attend');



Route::get('/home', [HomeController::class, 'index'])
    ->name('home');

Route::get('/register', [AuthController::class, 'registerView'])->name('register');
Route::post('/register', [AuthController::class, 'register']);

Route::get('/login', [AuthController::class, 'loginView'])->name('login');
Route::post('/login', [AuthController::class, 'login']);

Route::post('/logout', [AuthController::class, 'logout'])->name('logout');


    Route::get('/payment', [PaymentController::class, 'showPaymentForm'])->name('payment.form');
    Route::post('/payment', [PaymentController::class, 'initiatePayment'])->name('payment.initiate');
    Route::get('/payment/{id}', [PaymentController::class, 'showPaymentStatus'])->name('payment.status');


Route::post('/mpesa/callback', [PaymentController::class, 'handleCallback'])->name('payment.callback');