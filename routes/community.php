<?php

use App\Http\Controllers\Api\CommunityController;
use Illuminate\Support\Facades\Route;

Route::middleware('auth:sanctum')->group(function () {
    Route::get('home', [CommunityController::class, 'home']);
    Route::post('create', [CommunityController::class, 'create']);
    Route::post('edit', [CommunityController::class, 'edit']);
    Route::get('list/sponsor/{community_id}', [CommunityController::class, 'listSponsor']);
    Route::get('delete/sponsor/{sponsor_id}', [CommunityController::class, 'deleteSponsor']);
    Route::get('delete/picture/{picture_id}', [CommunityController::class, 'deletePicture']);
    Route::post('search', [CommunityController::class, 'search']);
    Route::get('category/search/{cat_id}', [CommunityController::class, 'categorySearch']);
    Route::get('list/{type}', [CommunityController::class, 'list']);
    Route::get('following/list/{community_id}', [CommunityController::class, 'listUser']);
    Route::get('send/invite/{community_id}/{to_id}', [CommunityController::class, 'sendInvite']);
    Route::post('search/users/{community_id}', [CommunityController::class, 'searchUsers']);
    Route::get('invited/communities/', [CommunityController::class, 'InvitedCommunity']);

});
