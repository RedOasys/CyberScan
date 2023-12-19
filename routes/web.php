<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FileUploadController;
use App\Http\Controllers\FileDisplayController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalysisController;

Auth::routes();

Route::middleware(['auth'])->group(function () {
    // Home route
    Route::get('/', [HomeController::class, 'index'])->name('home');

    // Display all files
    Route::get('/files', [FileDisplayController::class, 'index'])->name('files');

    // Upload Post
    Route::post('/upload', [FileUploadController::class, 'upload'])->name('file.upload');

    // Search files route
    Route::get('/search-files', [FileDisplayController::class, 'searchFiles'])->name('searchFiles');

    Route::get('/fetch-all-files', [FileDisplayController::class, 'fetchAllFiles'])->name('fetchAllFiles');

    Route::get('/dashboard-data', [DashboardController::class, 'dashboardData'])->name('dashboard-data');

    // Main Analysis Page
    Route::get('/analysis', [AnalysisController::class, 'index'])->name('analysis');

    // Subpage for creating analysis tasks
    Route::get('/analysis/tasks/create', [AnalysisController::class, 'createTask'])->name('analysis.tasks.create');

    // Route to handle the submission of the task creation form
    Route::post('/analysis/tasks/submit', [AnalysisController::class, 'submitTask'])->name('analysis.tasks.submit');

    // Subpage for viewing the task queue
    Route::get('/analysis/tasks/queue', [AnalysisController::class, 'taskQueue'])->name('analysis.tasks.queue');


    // Subpage for viewing the status of analysis tasks
    Route::get('/analysis/tasks/all', [AnalysisController::class, 'taskStatus'])->name('analysis.tasks.all');

    // Subpage for viewing logs
    Route::get('/analysis/logs', [AnalysisController::class, 'logs'])->name('analysis.logs');
    Route::get('/analysis/tasks/result/{analysisId}', [AnalysisController::class, 'showAnalysisResult'])->name('analysis.tasks.result');
    Route::get('/analysis/virustotal/{md5}', [AnalysisController::class, 'checkVirusTotal'])->name('analysis.virustotal');
    Route::get('/update-analysis/{analysis}', [AnalysisController::class, 'updateAnalysisRoute'])->name('analysis.update');
    Route::get('/analysis/tasks/queue/data', [AnalysisController::class, 'taskQueueData'])->name('analysis.tasks.queue.data');
    Route::get('/analysis/tasks/queue/finished', [AnalysisController::class, 'taskAnalyzedFiles'])->name('analysis.tasks.queue.finished');

    Route::get('/analysis/tasks/queue/finishedbrief', [AnalysisController::class, 'taskAnalyzedFilesBrief'])->name('analysis.tasks.queue.finishedbrief');

});

