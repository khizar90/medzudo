

<?php

use App\Http\Controllers\Api\MessageController;
use Illuminate\Support\Facades\Route;

Route::post('send', [MessageController::class, 'send']);
Route::get('list/{to_id}', [MessageController::class, 'conversation']);
Route::get('read/{from_to}', [MessageController::class, 'messageRead']);
Route::get('unified/inbox', [MessageController::class, 'unifiedInbox']);
Route::prefix('group')->group(function () {
    Route::get('list/{group_id}', [MessageController::class, 'groupConversation']);
    Route::get('read/{group_id}', [MessageController::class, 'groupMessageRead']);
});
