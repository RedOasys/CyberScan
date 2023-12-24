<?php

namespace App\Http\Controllers;

use Arcanedev\LogViewer\Contracts\LogViewer as LogViewerContract;
use Illuminate\Support\Facades\Log; // Import the Log facade

class LogController extends Controller
{
    protected $logViewer;

    public function __construct(LogViewerContract $logViewer)
    {
        $this->logViewer = $logViewer;
    }

    public function index()
    {
        $logs = $this->logViewer->all(); // Get all logs
        \Log::debug('Logs count: ' . count($logs));

        return view('logs', compact('logs'));
    }
}
