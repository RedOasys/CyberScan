<?php

namespace App\Http\Controllers;

use App\Services\CuckooService;

class CuckooController extends Controller
{
    protected $cuckooService;

    public function __construct(CuckooService $cuckooService)
    {
        $this->cuckooService = $cuckooService;
    }

    public function showDashboard()
    {
        $analyses = $this->cuckooService->getAnalyses();

        // Process $analyses to extract the needed information

        return view('dashboard', compact('analyses'));
    }
}
