<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\OTPController;

Route::get('/login', [OTPController::class, 'showLoginForm'])->name('login');
Route::post('/send-otp', [OTPController::class, 'sendOTP'])->name('send-otp');
Route::post('/verify-otp', [OTPController::class, 'verifyOTP'])->name('verify-otp');

Route::get('/', function () {
    return view('welcome');
});
