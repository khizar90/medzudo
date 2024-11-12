<?php

use App\Http\Controllers\Api\PostController;
use Illuminate\Support\Facades\Route;

Route::get('home', [PostController::class, 'home']);
Route::get('/suggestions', [PostController::class, 'suggestion']);
Route::post('create', [PostController::class, 'create']);
Route::get('repost/{id}', [PostController::class, 'repost']);
Route::get('detail/{post_id}', [PostController::class, 'detail']);
Route::get('like/{post_id}', [PostController::class, 'like']);
Route::get('like/list/{post_id}', [PostController::class, 'likeList']);
Route::get('save/{post_id}', [PostController::class, 'save']);
Route::get('delete/{post_id}', [PostController::class, 'delete']);
Route::post('comment', [PostController::class, 'comment']);
Route::get('comment/delete/{comment_id}', [PostController::class, 'deleteComment']);
Route::get('comment/list/{post_id}', [PostController::class, 'commentList']);
