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

        $fileUpload = FileUpload::find($validated['uploaded_file']);
        if (!$fileUpload) {
            return back()->withErrors('File not found.');
        }

        $filePath = storage_path('app/public/' . $fileUpload->file_path);
        if (!file_exists($filePath)) {
            return back()->withErrors('File not found on server.');
        }

        $settings = [
            'platforms' => [
                ['platform' => 'windows', 'os_version' => '10']
            ],
            'timeout' => (int)$validated['timeout'],
            // ... any additional settings ...
        ];

        if (!empty($validated['options'])) {
            $settings['options'] = json_decode($validated['options'], true);
        }

        $submitResponse = Http::withHeaders([
            'Authorization' => 'token ' . env('CUCKOO_API_TOKEN'),
        ])->attach(
            'file', file_get_contents($filePath), basename($filePath)
        )->post(env('CUCKOO_API_BASE_URL') . '/submit/file', [
            'settings' => json_encode($settings)
        ]);

        if ($submitResponse->successful()) {
            sleep(5); // Adjust as needed

            $analyses = $this->cuckooService->getAnalyses();
            $analysisId = null;
            foreach ($analyses['analyses'] as $analysis) {
                if ($analysis['target']['md5'] === $fileUpload->md5_hash) {
                    $analysisId = $analysis['id'];
                    break;
                }
            }

            if ($analysisId) {
                $newAnalysis = StaticAnalysis::create([
                    'file_upload_id' => $fileUpload->id,
                    'analysis_id' => $analysisId,
                    // ... other necessary fields ...
                ]);

                // Fetch and update analysis details immediately
                $this->updateAnalysisDetails($newAnalysis);

                return redirect()->route('analysis.tasks.result', ['analysisId' => $analysisId]);
            } else {
                return back()->withErrors('Failed to find the analysis for the submitted file.');
            }
        } else {
            return back()->withErrors('Failed to submit task. ' . $submitResponse->body());
        }
    }
    public function updateAnalysisRoute($analysisId)
    {
        $analysis = StaticAnalysis::where('analysis_id', $analysisId)->firstOrFail();
        $this->updateAnalysisDetails($analysis);
        return response()->json(['message' => 'Analysis updated successfully']);
    }
    protected function updateAnalysisDetails($analysis)
    {
        $response = Http::withHeaders([
            'Authorization' => 'token ' . env('CUCKOO_API_TOKEN'),
        ])->get(env('CUCKOO_API_BASE_URL') . '/analysis/' . $analysis->analysis_id);

        if ($response->successful()) {
            $analysisDetails = $response->json();

            // Assuming the response contains a 'submitted' section with an 'md5' hash
            $submitted = $analysisDetails['submitted'] ?? [];
            $fileUpload = FileUpload::where('md5_hash', $submitted['md5'] ?? '')->first();

            if ($fileUpload) {
                // Map the extracted data
                $updateData = [
                    'file_upload_id' => $fileUpload->id, // Associate with the correct file upload ID
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

                $analysis->update($updateData);

                Log::info('Updated analysis data for ID ' . $analysis->analysis_id);
            } else {
                Log::error('Failed to find matching file upload for analysis ID: ' . $analysis->analysis_id);
            }
        } else {
            Log::error('Failed to fetch details for analysis ID: ' . $analysis->analysis_id);
        }
    }
    public function checkVirusTotal($md5)
    {
        $apiKey = '2353478852f54143792270d40389c833ad993267b6d654167c306a44d5bf1591';
        $response = Http::withHeaders(['x-apikey' => $apiKey])
            ->get('https://www.virustotal.com/api/v3/files/' . $md5);

        if ($response->successful()) {
            $results = $response->json();

            // Analysis Logic
            $detectionCount = 0;
            $undetectedCount = 0;
            $kindCounts = [];

            foreach ($results['data']['attributes']['last_analysis_results'] as $engine => $result) {
                if ($result['category'] === 'malicious') {
                    $detectionCount++;
                    $kind = $result['result'] ?? 'unknown';
                    $kindCounts[$kind] = ($kindCounts[$kind] ?? 0) + 1;
                } else {
                    $undetectedCount++;
                }
            }

            $detection = $detectionCount > 0 ? 'Detected' : 'Undetected';
            $certainty = ($detectionCount + $undetectedCount) > 0 ? round($detectionCount / ($detectionCount + $undetectedCount) * 100, 2) : 0;
            arsort($kindCounts);
            $kind = key($kindCounts) ?: 'Unknown';

            // Pass the calculated data to the view
            return view('analysis.virustotal', compact('results', 'detection', 'certainty', 'kind'));
        } else {
            return back()->withErrors('Failed to fetch data from VirusTotal.');
        }
    }


    public function showAnalysisResult($analysisId)
    {
        $analysis = StaticAnalysis::where('analysis_id', $analysisId)->firstOrFail();
        return view('analysis.tasks.result', compact('analysis'));
    }

    public function taskQueueData(Request $request)
    {
        $analyses = StaticAnalysis::with('fileUpload')
            ->where('state', 'pending_pre')
            ->orWhere('state', 'tasks_pending')
            ->get();

        $data = $analyses->map(function ($analysis) {
            return [
                'DT_RowId' => 'row_' . $analysis->analysis_id,
                'id' => $analysis->id,
                'file_name' => $analysis->fileUpload ? $analysis->fileUpload->file_name : 'N/A',
                'status' => $analysis->state,
                'actions' => view('partials.analysis_actions', compact('analysis'))->render()
            ];
        });

        return response()->json([
            'draw' => intval($request->draw),
            'recordsTotal' => $analyses->count(),
            'recordsFiltered' => $analyses->count(),
            'data' => $data
        ]);
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
