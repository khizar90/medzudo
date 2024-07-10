<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Web\WebController;
use Illuminate\Support\Facades\Route;

Route::post('user/login', [WebController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::get('my/communities', [WebController::class, 'myCommunity']);
});
