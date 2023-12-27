<?php

namespace App\Services;

use App\Models\PostAnalysis;
use App\Models\StaticAnalysis;
use Illuminate\Support\Facades\Http;
use App\Models\PreAnalysis;
use Illuminate\Support\Facades\Log;

class CuckooService
{
    protected $baseUrl;
    protected $apiToken;

    public function __construct()
    {
        $this->baseUrl = env('CUCKOO_API_BASE_URL');
        $this->apiToken = env('CUCKOO_API_TOKEN');
    }

    public function getAnalyses()
    {
        $response = Http::withHeaders([
            'Authorization' => 'token ' . $this->apiToken,
        ])->get($this->baseUrl . '/analyses/');

        return $response->json();
    }
    public function createTask($file, $options)
    {
        $fullPath = storage_path('app/public/uploads/' . $file->file_name);

        if (!file_exists($fullPath)) {
            return ['error' => 'File not found'];
        }

        // Prepare settings data
        $settings = [
            'timeout' => $options['timeout'] ?? 120, // Default timeout if not provided
            'platforms' => [
                [
                    'platform' => 'windows',
                    'os_version' => '10' // Example version, adjust as needed
                ]
            ],
            // Add more settings as per your requirement
        ];

        if (!empty($options['machine'])) {
            // Include machine-specific settings if provided
            $settings['platforms'][0]['tags'] = [$options['machine']];
        }

        $response = Http::withHeaders([
            'Authorization' => 'token ' . $this->apiToken,
        ])->attach(
            'file', file_get_contents($fullPath), $file->file_name
        )->post($this->baseUrl . '/submit/file', [
            'settings' => json_encode($settings),
        ]);

        return $response->json();
    }
    public function fetchAndStorePreAnalysis($analysisId)
    {
        $baseUrl = env('CUCKOO_API_BASE_URL');
        $apiToken = env('CUCKOO_API_TOKEN');

        try {
            $response = Http::withHeaders([
                'Authorization' => 'token ' . $apiToken, // Match the format used in curl
            ])->get($baseUrl . '/analysis/' . $analysisId . '/pre');

            if ($response->failed()) {
                Log::error("HTTP request failed for analysis ID $analysisId", ['response' => $response->body()]);
                return null;
            }

            $data = $response->json();

            // Fetch the corresponding static_analysis record's ID
            $staticAnalysis = StaticAnalysis::where('analysis_id', $analysisId)->first();

            if (!$staticAnalysis) {
                Log::warning("No matching static analysis record found for analysis ID $analysisId");
                return null;
            }

            // Store the response in the database with static_analysis_id
            $preAnalysis = PreAnalysis::updateOrCreate(
                ['static_analysis_id' => $staticAnalysis->id], // Key to check
                ['data' => json_encode($data)] // Values to update or insert
            );


            $preAnalysis->save();

            return $preAnalysis;

        } catch (\Exception $e) {
            Log::error("Error in fetchAndStorePreAnalysis: " . $e->getMessage());
            return null;
        }
    }

    public function fetchAndStorePostAnalysis($analysisId)
    {
        $baseUrl = 'http://192.168.100.100:6942'; // Directly use the base URL
        $apiToken = 'ba9e193747a5e38281b688ebc748febdc5d7532c'; // Directly use the token

        try {
            $response = Http::withHeaders([
                'Authorization' => 'token ' . $apiToken, // Match the format used in curl
            ])->get($baseUrl . '/analysis/' . $analysisId . '/task/' . $analysisId . '_1/post');

            if ($response->failed()) {
                Log::error("HTTP request failed for analysis ID $analysisId", ['response' => $response->body()]);
                return null;
            }

            $data = $response->json();

            // Fetch the corresponding static_analysis record's ID
            $staticAnalysis = StaticAnalysis::where('analysis_id', $analysisId)->first();

            if (!$staticAnalysis) {
                Log::warning("No matching static analysis record found for analysis ID $analysisId");
                return null;
            }

            // Update an existing row or create a new one with static_analysis_id
            $postAnalysis = PostAnalysis::updateOrCreate(
                ['static_analysis_id' => $staticAnalysis->id], // Key to check
                ['data' => json_encode($data)] // Values to update or insert
            );

            return $postAnalysis;

        } catch (\Exception $e) {
            Log::error("Error in fetchAndStorePostAnalysis: " . $e->getMessage());
            return null;
        }
    }





    // Add other methods to interact with different API endpoints as needed
}
