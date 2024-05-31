<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CommunityController;
use App\Http\Controllers\Api\EventController;
use App\Http\Controllers\Api\ForumController;
use App\Http\Controllers\Api\MessageController;
use App\Http\Controllers\Api\NewsController;
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

Route::middleware('auth:sanctum')->group(function () {
    Route::post('user/logout', [AuthController::class, 'logout']);
    Route::post('user/delete', [AuthController::class, 'deleteAccount']);
});
Route::post('user/verify', [AuthController::class, 'verify']);
Route::post('otp/verify', [AuthController::class, 'otpVerify']);
Route::post('user/register', [AuthController::class, 'register']);
Route::post('user/login', [AuthController::class, 'login']);
Route::post('user/add/interest', [AuthController::class, 'addInterest']);
Route::get('user/get/interest/{user_id}', [AuthController::class, 'userInterest']);
Route::post('user/recover', [AuthController::class, 'recover']);
Route::post('user/new/password', [AuthController::class, 'newPassword']);

Route::get('blocklist/{id}', [AuthController::class, 'blockList']);
Route::post('change/password', [AuthController::class, 'changePassword']);
Route::post('edit/image', [AuthController::class, 'editImage']);
Route::get('remove/image/{id}', [AuthController::class, 'removeImage']);
Route::post('edit/profile', [AuthController::class, 'editProfile']);
Route::post('get/verify', [AuthController::class, 'getVerify']);
Route::post('user/add/detail', [AuthController::class, 'addDetail']);
Route::get('user/get/detail/{type}/{user_id}', [AuthController::class, 'getDetail']);
Route::get('user/delete/detail/{id}', [AuthController::class, 'deleteDetail']);
Route::post('add/department', [AuthController::class, 'addDepartment']);
Route::post('edit/department', [AuthController::class, 'editDepartment']);
Route::get('list/department/{id}', [AuthController::class, 'listDepartment']);
Route::get('list/department/users/{id}', [AuthController::class, 'listDepartmentUser']);
Route::post('department/add/user', [AuthController::class, 'addDepartmentUser']);
Route::get('department/delete/user/{id}', [AuthController::class, 'deleteDepartmentUser']);
Route::get('user/management/{id}', [AuthController::class, 'management']);
Route::post('user/add/management', [AuthController::class, 'addManagement']);
Route::post('user/edit/management', [AuthController::class, 'editManagement']);
Route::get('user/contact/{id}', [AuthController::class, 'contact']);
Route::post('user/add/contact', [AuthController::class, 'addContact']);
Route::post('user/edit/contact', [AuthController::class, 'editContact']);
Route::get('user/delete/gallery/{id}', [AuthController::class, 'deleteGallery']);
Route::post('user/verify', [AuthController::class, 'verify']);


Route::post('add/ticket', [TicketController::class, 'addTicket']);
Route::get('close/ticket/{ticket_id}', [TicketController::class, 'closeTicket']);
Route::get('conversation/{id}', [TicketController::class, 'conversation']);
Route::get('ticket/list/{id}/{status}', [TicketController::class, 'list']);

Route::post('send/message', [MessageController::class, 'sendMessage']);

Route::get('faqs', [SettingController::class, 'faqs']);
Route::get('splash/{user_id?}', [SettingController::class, 'splash']);
Route::get('categories/{type}', [SettingController::class, 'categories']);

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
Route::get('user/news/trendings/list/{user_id}', [NewsController::class, 'trending']);
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





Route::post('user/profile', [UserController::class, 'profile']);
Route::post('user/block', [UserController::class, 'block']);
Route::post('follow/user', [UserController::class, 'follow']);
Route::get('followers/{id}', [UserController::class, 'followers']);
Route::get('following/{id}', [UserController::class, 'following']);


Route::post('user/report', [ReportController::class, 'report']);




