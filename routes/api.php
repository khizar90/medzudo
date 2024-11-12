<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\TicketController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });



Route::post('add/ticket', [TicketController::class, 'addTicket']);
Route::get('close/ticket/{ticket_id}', [TicketController::class, 'closeTicket']);
Route::get('conversation/{id}', [TicketController::class, 'conversation']);
Route::get('ticket/list/{id}/{status}', [TicketController::class, 'list']);


Route::post('user/forum/create', [ForumController::class, 'create']);
Route::post('user/forum/edit', [ForumController::class, 'edit']);
Route::get('user/forum/delete/{id}', [ForumController::class, 'delete']);
Route::post('user/forum/vote', [ForumController::class, 'vote']);
Route::post('user/forum/comment', [ForumController::class, 'comment']);
Route::get('user/forum/comment/delete/{id}', [ForumController::class, 'commentDelete']);
Route::get('user/forum/detail/{user_id}/{forum_id}', [ForumController::class, 'detail']);
Route::get('user/forum/home/{user_id}', [ForumController::class, 'list']);
Route::post('user/forum/save', [ForumController::class, 'save']);
Route::get('user/forum/remove/image/{forum_id}', [ForumController::class, 'removeImage']);
Route::get('user/forum/list/{type}/{user_id}', [ForumController::class, 'userForum']);
Route::get('user/forum/category/search/{category_id}/{user_id}', [ForumController::class, 'categorySearch']);
Route::post('user/forum/search', [ForumController::class, 'search']);


Route::post('user/news/create', [NewsController::class, 'create']);
Route::post('user/news/edit', [NewsController::class, 'edit']);
Route::get('user/news/delete/{id}', [NewsController::class, 'delete']);
Route::post('user/news/like', [NewsController::class, 'like']);
Route::post('user/news/comment', [NewsController::class, 'comment']);
Route::get('user/news/comment/delete/{id}', [NewsController::class, 'commentDelete']);
Route::get('user/news/detail/{user_id}/{news_id}', [NewsController::class, 'detail']);
Route::get('user/news/home/{user_id}', [NewsController::class, 'list']);
Route::get('user/news/trending/list/{user_id}', [NewsController::class, 'trending']);
Route::post('user/news/save', [NewsController::class, 'save']);
// Route::get('user/news/list/{type}/{user_id}' , [NewsController::class , 'saveList']);
Route::get('user/news/list/{type}/{user_id}', [NewsController::class, 'userNews']);
Route::get('user/news/category/search/{category_id}/{user_id}', [NewsController::class, 'categorySearch']);
Route::post('user/news/search', [NewsController::class, 'search']);


Route::post('user/create/event', [EventController::class, 'create']);
Route::post('user/event/add/questions', [EventController::class, 'addQuestion']);
Route::post('user/event/edit', [EventController::class, 'edit']);
Route::get('user/event/delete/{id}', [EventController::class, 'delete']);
Route::post('user/event/edit/questions', [EventController::class, 'editQuestion']);
Route::get('user/event/delete/question/{que_id}', [EventController::class, 'deleteQuestion']);
Route::get('user/event/detail/{user_id}/{event_id}', [EventController::class, 'detail']);
Route::post('user/event/save', [EventController::class, 'save']);
Route::get('user/event/list/{type}/{user_id}', [EventController::class, 'saveList']);
Route::get('user/event/{user_id}/{status}', [EventController::class, 'userEvents']);
Route::post('user/event/search', [EventController::class, 'search']);
Route::post('user/event/home', [EventController::class, 'home']);
Route::get('user/event/join/{event_id}/{user_id}', [EventController::class, 'joinEvent']);
Route::get('event/join/members/{event_id}', [EventController::class, 'members']);

Route::post('user/report', [ReportController::class, 'report']);
