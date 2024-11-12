<?php

use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;
use Illuminate\Support\Facades\Route;

Route::get('splash/{user_id?}', [SettingController::class, 'splash']);
Route::get('categories/{type}', [SettingController::class, 'categories']);
Route::get('faqs', [SettingController::class, 'faqs']);
Route::post('report', [ReportController::class, 'report'])->middleware('auth:sanctum');
