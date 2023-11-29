<?php

use Illuminate\Http\Request;
use App\Http\Controllers\FileUploadController;
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


Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->name('home');

Auth::routes();


Route::get('/upload', [FileUploadController::class, 'index'])->name('upload')->middleware('auth');
Route::post('/upload/file',[FileUploadController::class, 'upload'])->name('upload.submit')->middleware('auth');
Route::middleware(['auth'])->group(function () {
    // Protected routes here
    Route::get('/upload', [FileUploadController::class, 'index'])->name('upload');
    Route::post('/upload/file', [FileUploadController::class, 'upload'])->name('upload.submit');
});
