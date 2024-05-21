<?php

use App\Http\Controllers\Api\CommunityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::post('create', [CommunityController::class, 'create']);
});
