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

Auth::routes();
Route::get('/', [App\Http\Controllers\HomeController::class, 'index'])->middleware('auth')->name('home');
Route::get('/upload', [FileUploadController::class, 'index'])->name('upload')->middleware('auth');
Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload')->middleware('auth');


