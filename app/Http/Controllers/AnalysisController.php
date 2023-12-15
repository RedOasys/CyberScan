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
        if (!$fileUpload) {
            return back()->withErrors('File not found.');
        }

        // Construct the file path
        $filePath = storage_path('app/public/' . $fileUpload->file_path);
        if (!file_exists($filePath)) {
            return back()->withErrors('File not found on server.');
        }

        // Prepare settings for analysis
        $settings = [
            'platforms' => [
                ['platform' => 'windows', 'os_version' => '10']
            ],
            'timeout' => (int)$validated['timeout'], // Cast timeout to integer
            // ... any additional settings ...
        ];

        if (!empty($validated['options'])) {
            $settings['options'] = json_decode($validated['options'], true);
        }

        // Submit the file for analysis
        $submitResponse = Http::withHeaders([
            'Authorization' => 'token ' . env('CUCKOO_API_TOKEN'),
        ])->attach(
            'file', file_get_contents($filePath), basename($filePath)
        )->post(env('CUCKOO_API_BASE_URL') . '/submit/file', [
            'settings' => json_encode($settings)
        ]);

        // Check if the submission was successful
        if ($submitResponse->successful()) {
            // Wait for a short period to ensure the analysis is created
            sleep(5); // Adjust the sleep duration as needed

            // Fetch the analyses to find the newly created one
            $analyses = $this->cuckooService->getAnalyses();

            // Find the analysis with the matching MD5 hash
            $analysisId = null;
            foreach ($analyses['analyses'] as $analysis) {
                if ($analysis['target']['md5'] === $fileUpload->md5_hash) {
                    $analysisId = $analysis['id'];
                    break;
                }
            }

            if ($analysisId) {
                // Create a new StaticAnalysis record with the file upload ID and analysis ID
                StaticAnalysis::create([
                    'file_upload_id' => $fileUpload->id,
                    'analysis_id' => $analysisId,
                    // ... other necessary fields ...
                ]);

                return back()->with('message', 'Task submitted successfully!, Analysis ID is: ' . $analysisId );
            } else {
                return back()->withErrors('Failed to find the analysis for the submitted file.');
            }
        } else {
            return back()->withErrors('Failed to submit task. ' . $submitResponse->body());
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
                            'analysis_id' => $analysisDetails['id'] ?? null,
                            'score' => $analysisDetails['score'] ?? 0,
                            'kind' => $analysisDetails['kind'] ?? null,
                            'state' => $analysisDetails['state'] ?? null,
                            'media_type' => $submitted['media_type'] ?? null,
                            'md5' => $submitted['md5'] ?? null,
                            'sha1' => $submitted['sha1'] ?? null,
                            'sha256' => $submitted['sha256'] ?? null,
                            'created_at' => $analysisDetails['created_on'] ?? null,
                            'updated_at' => now(),
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
