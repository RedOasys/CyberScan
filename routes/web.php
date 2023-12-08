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
