<?php

use App\Http\Controllers\Api\GroupController;
use Illuminate\Support\Facades\Route;

Route::post('create', [GroupController::class, 'create']);
Route::post('edit/{group_id}', [GroupController::class, 'edit']);
Route::get('detail/{group_id}', [GroupController::class, 'detail']);
Route::get('delete/{group_id}', [GroupController::class, 'delete']);
Route::get('participant/list/{group_id}', [GroupController::class, 'participantList']);
Route::get('search/participant/{group_id}', [GroupController::class, 'searchParticipant']);
Route::post('add/participant/{group_id}', [GroupController::class, 'addParticipant']);
Route::get('leave/{group_id}', [GroupController::class, 'leave']);
Route::get('remove/participant/{group_id}/{user_id}', [GroupController::class, 'removeParticipant']);
Route::get('list/users', [GroupController::class, 'listUsers']);
