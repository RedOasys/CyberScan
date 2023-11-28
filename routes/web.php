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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/upload', [FileUploadController::class, 'index'])->name('upload')->middleware('auth');
Route::post('/upload/file',[FileUploadController::class, 'upload'])->name('upload.submit')->middleware('auth');