<?php

use App\Http\Controllers\Admin\AdminCommunityController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Api\Web\ShareableController;
use Illuminate\Support\Facades\Route;

Route::view('/', 'welcome');

Route::get('apple-app-site-association', function () {
    $data = new stdClass();
    $applinks = new stdClass();
    $applinks->apps = [];
    $details = [];
    $obj = new stdClass();
    $obj->appID = '4354C49Z92.com.medzudo.application';
    $obj->paths = ['/*'];
    $details[] = $obj;
    $applinks->details = $details;
    $data->applinks = $applinks;
    return response()->json($data);
});

Route::get('user/shareable/{type}/{loginId}/{id}', [ShareableController::class, 'user']);
Route::get('shareable/{type}/{loginId}/{id}', [ShareableController::class, 'other']);

Route::prefix('dashboard')->middleware(['auth'])->name('dashboard-')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::get('users/{type}', [AdminController::class, 'users'])->name('users');
    Route::get('user/{type}/profile/{user_id}', [AdminController::class, 'userProfile'])->name('user-profile');
    Route::get('users/export', [AdminController::class, 'exportCSV'])->name('users-export-csv');
    Route::get('verify-users', [AdminController::class, 'verifyUsers'])->name('verify-users');
    Route::get('get-verify/{type}/{id}', [AdminController::class, 'getVerify'])->name('get-verify');
    Route::get('user/delete/{id}', [AdminController::class, 'deleteUser'])->name('delete-user');

    Route::get('user/verify/organization', [AdminController::class, 'organizationVerify'])->name('user-organization-verify');


    Route::prefix('report')->name('report-')->group(function () {
        Route::get('/{type}', [AdminReportController::class, 'report']);
        Route::get('delete/{id}', [AdminReportController::class, 'deleteReport'])->name('delete-report');
        Route::get('user/delete/{user_id}/{report_id}', [AdminReportController::class, 'deleteUser'])->name('delete-user');
        Route::get('forum/delete/{forum_id}/{report_id}', [AdminReportController::class, 'deleteForum'])->name('delete-forum');
        Route::get('news/delete/{news_id}/{report_id}', [AdminReportController::class, 'deleteNews'])->name('delete-news');
        Route::get('event/delete/{event_id}/{report_id}', [AdminReportController::class, 'deleteEvent'])->name('delete-event');
    });

    Route::prefix('community')->name('community-')->group(function () {
        Route::get('/', [AdminCommunityController::class, 'analytics']);
        Route::get('list', [AdminCommunityController::class, 'list'])->name('list');
        Route::get('/delete/{community_id}', [AdminCommunityController::class, 'delete'])->name('delete');
    });

    Route::prefix('faqs')->name('faqs-')->group(function () {
        Route::get('/', [AdminController::class, 'faqs']);
        Route::post('add', [AdminController::class, 'addFaq'])->name('add');
        Route::post('edit/{id}', [AdminController::class, 'editFaq'])->name('edit');
        Route::get('delete-faq/{id}', [AdminController::class, 'deleteFaq'])->name('delete');
    });


    Route::prefix('ticket')->name('ticket-')->group(function () {
        Route::get('ticket/{status}', [AdminTicketController::class, 'ticket'])->name('ticket');
        Route::get('close-ticket/{id}', [AdminTicketController::class, 'closeTicket'])->name('close-ticket');
        Route::get('messages/{from_to}', [AdminTicketController::class, 'messages'])->name('messages');
        Route::post('send-message', [AdminTicketController::class, 'sendMessage'])->name('send-message');
    });
    Route::prefix('version')->name('version-')->group(function () {
        Route::get('/{type}', [AdminController::class, 'version']);
        Route::post('save/{type}', [AdminController::class, 'versionSave'])->name('save');
    });
    Route::get('emergency', [AdminController::class, 'emergency'])->name('emergency');
    Route::get('emergency/check/{name}/{value}', [AdminController::class, 'emergencyCheck'])->name('emergency-check');
    Route::post('emergency/message/', [AdminController::class, 'emergencyMessage'])->name('emergency-message');
    
    Route::get('send-notification', [AdminController::class, 'createSendNotification'])->name('send-notification');
    Route::post('send-notification', [AdminController::class, 'sendNotification'])->name('create-notification');
});

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });




require __DIR__ . '/auth.php';
