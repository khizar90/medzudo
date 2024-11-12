
<?php

use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::post('update', [UserController::class, 'updateUser']);
// Route::post('update/image', [UserController::class, 'editImage']);
Route::get('remove/image/{type}', [UserController::class, 'removeImage']);
Route::post('get/verify', [UserController::class, 'getVerify']);
Route::post('change/password', [UserController::class, 'changePassword']);
Route::post('delete', [UserController::class, 'deleteAccount']);
Route::post('logout', [UserController::class, 'logout']);
Route::post('set/profile', [UserController::class, 'setProfile']);
Route::post('complete/profile', [UserController::class, 'completeProfile']);
Route::get('follow/{to_id}', [UserController::class, 'follow']);
Route::get('block-list', [UserController::class, 'blockList']);
Route::get('block/{block_id}', [UserController::class, 'block']);
Route::get('followers/{user_id}', [UserController::class, 'followers']);
Route::get('following/{user_id}', [UserController::class, 'following']);
Route::get('get/interest', [UserController::class, 'userInterest']);
Route::get('profile/{to_id}/{type}', [UserController::class, 'profile']);
Route::get('counter', [UserController::class, 'unreadCounter']);


Route::post('create/detail', [UserController::class, 'addDetail']);
Route::post('edit/detail/{id}', [UserController::class, 'editDetail']);
Route::get('get/detail/{type}', [UserController::class, 'getDetail']);
Route::get('delete/detail/{id}', [UserController::class, 'deleteDetail']);

Route::post('global/search', [UserController::class, 'globalSearch']);



Route::prefix('discover/')->group(function () {
    Route::get('/', [UserController::class, 'discover']);
    Route::post('/list/{type}', [UserController::class, 'discoverList']);
    Route::post('/search/{type}', [UserController::class, 'discoverSearch']);
});

Route::prefix('contact')->group(function () {
    Route::get('list', [UserController::class, 'contact']);
    Route::post('create', [UserController::class, 'addContact']);
    Route::post('edit/{id}', [UserController::class, 'editContact']);
    Route::get('delete/{id}', [UserController::class, 'deleteContact']);
});
Route::prefix('management')->group(function () {
    Route::get('list/', [UserController::class, 'management']);
    Route::post('create', [UserController::class, 'addManagement']);
    Route::post('edit/{id}', [UserController::class, 'editManagement']);
    Route::get('delete/{id}', [UserController::class, 'deleteManagement']);
});
Route::prefix('department')->group(function () {
    Route::get('list', [UserController::class, 'department']);
    Route::post('create', [UserController::class, 'addDepartment']);
    Route::post('edit/{id}', [UserController::class, 'editDepartment']);
    Route::get('delete/{id}', [UserController::class, 'deleteDepartment']);
    Route::prefix('member')->group(function () {
        Route::get('list/{department_id?}', [UserController::class, 'departmentMember']);
        Route::post('create', [UserController::class, 'addDepartmentMember']);
        Route::post('edit/{id}', [UserController::class, 'editDepartmentMember']);
        Route::get('delete/{id}', [UserController::class, 'deleteDepartmentMember']);
    });
});

Route::post('list/member/{type}', [UserController::class, 'listDMTUsers']);
Route::post('search/member/{type}', [UserController::class, 'searchDMTUsers']);


Route::post('add/media/', [UserController::class, 'addMedia']);
Route::get('list/media/', [UserController::class, 'listMedia']);
Route::get('delete/media/{id}', [UserController::class, 'deleteMedia']);

Route::get('notification/{type}', [UserController::class, 'notification']);

Route::get('remind/me', [UserController::class, 'remindMe']);
