

<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('verify', [AuthController::class, 'verify']);
Route::post('otp/verify', [AuthController::class, 'otpVerify']);
Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('add/interest', [AuthController::class, 'addInterest'])->middleware('auth:sanctum');
Route::post('recover', [AuthController::class, 'recover']);
Route::post('new/password', [AuthController::class, 'newPassword']);
Route::post('update/fcm', [AuthController::class, 'updateFcm']);
