<?php

namespace App\Http\Controllers;

use App\Models\Detection;
use App\Models\FileUpload;
use App\Models\StaticAnalysis;
use Illuminate\Http\Request;
use App\Services\CuckooService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;




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
            sleep(0.05); // Adjust as needed

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

                ]);

                $vt = $this->checkVT($fileUpload->md5_hash);
                $detective = $vt['detection'] == 'Detected' ? 1 : 0;
                $detection = Detection::create([
                    'file_upload_id' => $fileUpload->id,
                    'analysis_id' => $newAnalysis->id,
                    'detected' => $detective,
                    'malware_type' => $vt['kind'],
                    'certainty' => $vt['certainty'],
                    'source' => $vt['source'], // Use the source from the analysis results
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

    public function checkVT($md5)
    {
        if ($this->canUseVT()) {
            $apiKey = env('VT_API_KEY');
            $response = Http::withHeaders(['x-apikey' => $apiKey])
                ->get('https://www.virustotal.com/api/v3/files/' . $md5);

            if ($response->successful()) {
                $this->incrementVTRequestCount();
                $results = $response->json();
                $vtResponse = $this->parseVTResponse($results);
                $vtResponse['source'] = 'Static'; // Add the source as 'VT'
                return $vtResponse;
            }
        }

        return $this->checkCymru($md5);
    }

    protected function canUseVT()
    {
        $limit = 500;
        $currentCount = Cache::get('vt_request_count', 0);
        return $currentCount < $limit;
    }

    protected function incrementVTRequestCount()
    {
        $count = Cache::get('vt_request_count', 0);
        Cache::put('vt_request_count', $count + 1, now()->endOfDay());
    }

    protected function checkCymru($md5)
    {
        $user = env('cymru_user');
        $password = env('cymru_password');
        $response = Http::withOptions([
            'verify' => false, // Disable SSL certificate verification
        ])->withBasicAuth($user, $password)
            ->get("https://hash.cymru.com/v2/$md5");


        if ($response->successful()) {
            $results = $response->json();
            // Parse the Cymru response and return similar structure as VT
            return $this->parseCymruResponse($results);
        } else {
            return []; // Or handle the error appropriately
        }
    }

    public function checkVirusTotal($md5)
    {
        $apiKey = env('VT_API_KEY');
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

    protected function parseVTResponse($results)
    {
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

        return [
            'detection' => $detection,
            'certainty' => $certainty,
            'kind' => $kind,
        ];
    }

    protected function parseCymruResponse($results)
    {
        $detection = $results['antivirus_detection_rate'] > 0 ? 'Detected' : 'Undetected';
        $certainty = $results['antivirus_detection_rate'];
        $kind = 'Unknown'; // Cymru doesn't provide specific malware types, so we default to 'Unknown'

        return [
            'detection' => $detection,
            'certainty' => $certainty,
            'kind' => $kind,
            'source' => 'Static', // Add the source as 'Cymru'
        ];
    }
















    public function showAnalysisResult($analysisId)
    {
        $analysis = StaticAnalysis::where('analysis_id', $analysisId)->firstOrFail();
        return view('analysis.tasks.result', compact('analysis'));
    }

    public function taskQueueData(Request $request)
    {
        // Fetch parameters from DataTables request
        $start = $request->input('start', 0);
        $length = $request->input('length', 10);
        $searchValue = $request->input('search.value'); // Get the search value

        // Build the query
        $query = StaticAnalysis::with('fileUpload')
            ->where(function ($query) use ($searchValue) {
                if ($searchValue) {
                    $query->where('analysis_id', 'like', '%' . $searchValue . '%')
                        ->orWhereHas('fileUpload', function ($q) use ($searchValue) {
                            $q->where('file_name', 'like', '%' . $searchValue . '%');
                        });
                }
            })
            ->where(function ($query) {
                $query->where('state', 'pending_pre')
                    ->orWhere('state', 'tasks_pending');
            });

        // Get total count of records
        $recordsTotal = StaticAnalysis::count();
        $recordsFiltered = $query->count();

        // Apply pagination
        $analyses = $query->skip($start)->take($length)->get();

        // Map the data for DataTables
        $data = $analyses->map(function ($analysis) {
            return [
                'DT_RowId' => 'row_' . $analysis->analysis_id,
                'analysis_id' => $analysis->analysis_id,
                'file_name' => $analysis->fileUpload ? $analysis->fileUpload->file_name : 'N/A',
                'status' => $analysis->state,
                'actions' => view('partials.analysis_actions', compact('analysis'))->render()
            ];
        });

        // Return JSON response
        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    public function getAnalyzedSamplesCount()
    {
        $count = StaticAnalysis::where('state', 'finished')->count();
        return response()->json(['analyzedSamplesCount' => $count]);
    }

    public function taskQueueDataBrief(Request $request)
    {
        $analyses = StaticAnalysis::with('fileUpload')
            ->where('state', 'pending_pre')
            ->orWhere('state', 'tasks_pending')->orderBy('id', 'desc')
            ->take(5) ->get();

        $data = $analyses->map(function ($analysis) {
            return [
                'DT_RowId' => 'row_' . $analysis->analysis_id,
                'analysis_id' => $analysis->analysis_id,
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

    public function taskAnalyzedFilesBrief(Request $request)
    {
        $analyses = StaticAnalysis::with('fileUpload')
            ->where('state', 'finished')->orderBy('id', 'desc')->
            take(5)->get();

        $data = $analyses->map(function ($analysis) {
            return [
                'DT_RowId' => 'row_' . $analysis->analysis_id,
                'analysis_id' => $analysis->analysis_id,
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

    public function taskAnalyzedFiles(Request $request)
    {
        $start = $request->input('start'); // Starting point of records
        $length = $request->input('length'); // Number of records to fetch
        $searchValue = $request->input('search.value'); // Get the search value

        // Build the initial query
        $query = StaticAnalysis::with('fileUpload')
            ->where('state', '=', 'finished') // Ensure 'state' matches your column name
            ->orderBy('id', 'desc');

        // Filter query based on the search value
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->where('analysis_id', 'LIKE', "%{$searchValue}%")
                    ->orWhereHas('fileUpload', function ($q) use ($searchValue) {
                        $q->where('file_name', 'LIKE', "%{$searchValue}%");
                    });
            });
        }




        // Get filtered count
        $recordsFiltered = $query->count();

        // Apply pagination and get results
        $analyses = StaticAnalysis::with('fileUpload')
            ->where('state', 'finished')->orderBy('id', 'desc')->
            get();

        $analysesCollection = collect($analyses);


        // Map the data for DataTables
        $data = $analysesCollection->map(function ($analysis) {

            return [
                'DT_RowId' => 'row_' . $analysis->analysis_id,
                'analysis_id' => $analysis->analysis_id,
                'file_name' => $analysis->fileUpload ? $analysis->fileUpload->file_name : 'N/A',
                'created_at' => $analysis->created_at,
                'status' => $analysis->state,
                'actions' => view('partials.analysis_actions', compact('analysis'))->render()
            ];
        });

        return response()->json([
            'draw' => intval($request->input('draw')),
            'recordsTotal' => StaticAnalysis::where('state', 'finished')->count(),
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }

    public function hasAnalysis($fileId) {
        $analysisExists = StaticAnalysis::where('file_upload_id', $fileId)->exists();
        return response()->json(['hasAnalysis' => $analysisExists]);
    }

    public function fetchStaticAnalysisForFile($fileId) {
        $staticAnalysis = StaticAnalysis::where('file_upload_id', $fileId)->first();

        if (!$staticAnalysis) {
            return response()->json(['exists' => false]);
        }

        return response()->json([
            'exists' => true,
            'analysisId' => $staticAnalysis->analysis_id,
            'md5' => $staticAnalysis->md5,
        ]);
    }

    public function updateAnalysisRoute($analysisId)
    {
        $analysis = StaticAnalysis::where('analysis_id', $analysisId)->firstOrFail();
        $this->updateAnalysisDetails($analysis);

        // Fetch and store pre-analysis data
        $preAnalysisData = $this->cuckooService->fetchAndStorePreAnalysis($analysisId);

        // Fetch and store post-analysis data
        $postAnalysisData = $this->cuckooService->fetchAndStorePostAnalysis($analysisId);


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
                dd($updateData['score']);
                // Check if the detection model needs to be updated
                if ($updateData['score'] >= 8 && $fileUpload->detection && $fileUpload->detection->detected == 0) {
                    $certainty = 0;
                    dd($updateData['score']);
                    if ($updateData['score'] == 8) {
                        dd($updateData['score']);
                        $certainty = 60;
                    } elseif ($updateData['score'] == 9) {
                        dd($updateData['score']);
                        $certainty = 80;
                    } elseif ($updateData['score'] == 10) {
                        dd($updateData['score']);
                        $certainty = 90;
                    }



                    Log::info('Updated detection data for file_upload_id: ' . $fileUpload->id);
                }

                Log::info('Updated analysis data for ID ' . $analysis->analysis_id);
            } else {
                Log::error('Failed to find matching file upload for analysis ID: ' . $analysis->analysis_id);
            }
        } else {
            Log::error('Failed to fetch details for analysis ID: ' . $analysis->analysis_id);
        }
    }

    public function getScreenshot($task_id, $filename)
    {
        $baseUrl = env('CUCKOO_API_BASE_URL', 'http://192.168.100.100:6942');
        $apiKey = env('CUCKOO_API_TOKEN', '');

        // Construct the API URL
        $apiUrl = "{$baseUrl}/analysis/" . explode('_', $task_id)[0] . "/task/{$task_id}/screenshot/{$filename}";

        // Make the HTTP request to the API
        $response = Http::withHeaders(['Authorization' => 'token ' . $apiKey])->get($apiUrl);

        if ($response->successful()) {
            // Return the image content as a response
            return new Response($response->body(), 200, ['Content-Type' => 'image/jpeg']);
        }

        return response('Image not found', 404);
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
        return view('analysis.tasks.all');
    }


}
