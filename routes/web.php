<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FileDisplayController;
use App\Http\Controllers\DashboardController;

Auth::routes();

// Home route
Route::get('/', [HomeController::class, 'index'])->middleware('auth')->name('home');

// Display all files
Route::get('/files', [FileDisplayController::class, 'index'])->name('files');

// Upload Post
Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload');

// Search files route
Route::get('/search-files', [FileDisplayController::class, 'searchFiles'])->name('searchFiles');

Route::get('/fetch-all-files', [FileDisplayController::class, 'fetchAllFiles'])->name('fetchAllFiles');

Route::get('/dashboard-data', [DashboardController::class, 'dashboardData'])->name('dashboard-data');

