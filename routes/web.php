<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\HomeController;

Auth::routes();

// Home route
Route::get('/', [HomeController::class, 'index'])->middleware('auth')->name('home');

// File upload routes
Route::middleware('auth')->group(function () {
    Route::get('/upload', [FileUploadController::class, 'index'])->name('upload');
    Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload');
});
Route::get('/analysis', function () {
    return view('analysis');
})->middleware('auth')->name('analysis');

// web.php

Route::post('/analyze', [FileUploadController::class, 'analyze'])->name('analyze')->middleware('auth');


Route::get('/files', [FileUploadController::class, 'showFiles'])->middleware('auth')->name('files');
Route::post('/files/delete/{id}', [FileUploadController::class, 'deleteFile'])->name('files.delete');
Route::get('/files/view/{id}', [FileUploadController::class, 'viewFile'])->name('files.view');
Route::get('/files/data', [FileUploadController::class, 'getTableData'])->name('files.data');
