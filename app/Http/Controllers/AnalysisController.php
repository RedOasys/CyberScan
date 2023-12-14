<?php

namespace App\Http\Controllers;

use App\Models\FileUpload;
use App\Models\StaticAnalysis;
use Illuminate\Http\Request;
use App\Services\CuckooService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;


class AnalysisController extends Controller
{
    protected $cuckooService;

    public function __construct(CuckooService $cuckooService)
    {
        $this->cuckooService = $cuckooService;
    }

    public function submitTask(Request $request)
    {
        $validated = $request->validate([
            'uploaded_file' => 'required|integer',
            'timeout' => 'required|integer',
            'machine' => 'required|string',
            'options' => 'nullable|string'
        ]);

        // Find the file upload record based on the selected ID from the dropdown
        $fileUpload = FileUpload::find($validated['uploaded_file']);

        // Construct the file path
        $filePath = storage_path('app/public/' . $fileUpload->file_path);

        // Check if the file exists
        if (!file_exists($filePath)) {
            return back()->withErrors('File not found.');
        }

        $settings = [
            'platforms' => [
                ['platform' => 'windows', 'os_version' => '10']
            ],
            'timeout' => (int)$validated['timeout'] // Cast timeout to integer
        ];

        if (!empty($validated['options'])) {
            $settings['options'] = json_decode($validated['options'], true);
        }

        $response = Http::withHeaders([
            'Authorization' => 'token ' . env('CUCKOO_API_TOKEN'),
        ])->attach(
            'file', file_get_contents($filePath), basename($filePath)
        )->post(env('CUCKOO_API_BASE_URL') . '/submit/file', [
            'settings' => json_encode($settings)
        ]);

        if ($response->successful()) {
            $this->populatePreAnalysisData();
            return back()->with('message', 'Task submitted successfully!');
        } else {
            return back()->withErrors('Failed to submit task. ' . $response->body());
        }

    }
    public function populatePreAnalysisData()
    {
        // Fetch analyses from the API
        $response = Http::withHeaders([
            'Authorization' => 'token ' . env('CUCKOO_API_TOKEN'),
        ])->get(env('CUCKOO_API_BASE_URL') . '/analyses/');

        if ($response->successful()) {
            $analysesData = $response->json()['analyses'];

            foreach ($analysesData as $analysis) {
                $fileUpload = FileUpload::where('md5_hash', $analysis['target']['md5'])->first();

                if ($fileUpload) {
                    $existingAnalysis = StaticAnalysis::where('analysis_id', $analysis['id'])->first();

                    if (!$existingAnalysis) {
                        $newAnalysisData = [
                            'file_upload_id' => $fileUpload->id,
                            'analysis_id' => $analysis['id'],
                            'score' => $analysis['score'] ?? 0,
                            'kind' => $analysis['kind'] ?? null,
                            'state' => $analysis['state'] ?? null,
                            'media_type' => $analysis['target']['media_type'] ?? null,
                            'md5' => $analysis['target']['md5'] ?? null,
                            'sha1' => $analysis['target']['sha1'] ?? null,
                            'sha256' => $analysis['target']['sha256'] ?? null,
                            // ... more fields ...
                        ];

                        // Log the data to be inserted
                        Log::info('Inserting new analysis data: ', $newAnalysisData);

                        StaticAnalysis::create($newAnalysisData);
                    }
                }
            }

            return back()->with('message', 'Pre-analysis data populated successfully.');
        } else {
            return back()->withErrors('Failed to fetch analysis data. ' . $response->body());
        }
    }




    public function index()
    {
        return view('analysis.index'); // Corresponding view for the main analysis page
    }

    public function createTask()
    {
        $unanalyzedFiles = FileUpload::doesntHave('staticAnalysis')->get();
        return view('analysis.tasks.create', compact('unanalyzedFiles'));
    }

    public function taskQueue()
    {
        return view('analysis.tasks.queue'); // View for the analysis task queue
    }

    public function taskStatus()
    {
        return view('analysis.tasks.status'); // View for the analysis task status
    }

    public function logs()
    {
        return view('analysis.logs'); // View for logs
    }
}
