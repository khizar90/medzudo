<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\AdminReportController;
use App\Http\Controllers\Admin\AdminTicketController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\ProfileController;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/insert', function () {
    $user = new Admin();
    $user->name = 'Kevin Anderson';
    $user->email = 'admin@admin.com';
    $user->password = Hash::make('qweqwe');
    $user->save();
});
Route::get('/', function () {
    return view('welcome');
});

Route::prefix('dashboard')->middleware(['auth'])->name('dashboard-')->group(function () {
    Route::get('/', [AdminController::class, 'index']);
    Route::get('users/{type}', [AdminController::class, 'users'])->name('users');
    Route::get('users/export', [AdminController::class, 'exportCSV'])->name('users-export-csv');
    Route::get('verify-users', [AdminController::class, 'verifyUsers'])->name('verify-users');
    Route::get('get-verify/{type}/{id}', [AdminController::class, 'getVerify'])->name('get-verify');

    Route::get('user/verify/organization', [AdminController::class, 'organizationVerify'])->name('user-organization-verify');


    Route::prefix('report')->name('report-')->group(function () {
        Route::get('/{type}', [AdminReportController::class, 'report']);
        Route::get('delete/{id}', [AdminReportController::class, 'deleteReport'])->name('delete-report');
        Route::get('user/delete/{user_id}/{report_id}', [AdminReportController::class, 'deleteUser'])->name('delete-user');
        Route::get('forum/delete/{forum_id}/{report_id}', [AdminReportController::class, 'deleteForum'])->name('delete-forum');
        Route::get('news/delete/{news_id}/{report_id}', [AdminReportController::class, 'deleteNews'])->name('delete-news');
        Route::get('event/delete/{event_id}/{report_id}', [AdminReportController::class, 'deleteEvent'])->name('delete-event');
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

    Route::prefix('category')->name('category-')->group(function () {
        Route::get('/{type}', [CategoryController::class, 'list']);
        Route::post('/add', [CategoryController::class, 'add'])->name('add');
        Route::post('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
        Route::get('/delete/{id}', [CategoryController::class, 'delete'])->name('delete');
    });

    Route::get('send-notification', [AdminController::class, 'createSendNotification'])->name('send-notification');
    Route::post('send-notification', [AdminController::class, 'sendNotification'])->name('create-notification');
});

// Route::middleware('auth')->group(function () {
//     Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
//     Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
//     Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
// });




require __DIR__ . '/auth.php';
