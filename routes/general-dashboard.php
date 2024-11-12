
<?php

use App\Http\Controllers\Admin\CategoryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('category-','interest');
});
Route::prefix('category')->name('category-')->group(function () {
    Route::get('/{type}', [CategoryController::class, 'list']);
    Route::post('/add', [CategoryController::class, 'add'])->name('add');
    Route::post('csv/add/{type}', [CategoryController::class, 'addCsv'])->name('add-csv');
    Route::post('/edit/{id}', [CategoryController::class, 'edit'])->name('edit');
    Route::get('/delete/{id}', [CategoryController::class, 'delete'])->name('delete');
    Route::prefix('sub')->name('sub-')->group(function () {
        Route::get('/{type}/{id}', [CategoryController::class, 'subList']);
        Route::post('/create', [CategoryController::class, 'subCreate'])->name('create');
    });
});
