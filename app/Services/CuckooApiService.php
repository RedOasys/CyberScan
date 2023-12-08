<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class CuckooApiService
{
    protected $baseUrl;
    protected $apiKey;

    public function __construct()
    {
        $this->baseUrl = env('CUCKOO_API_BASE_URL');
        $this->apiKey = env('CUCKOO_API_TOKEN');
    }

    public function submitFile($filePath, $additionalSettings = [])
    {
        // Default settings if not provided
        if (empty($additionalSettings)) {
            $additionalSettings = [
                'platforms' => [
                    ['platform' => 'windows', 'os_version' => '10']
                ],
                'timeout' => 120
            ];
        }

        $url = $this->baseUrl . '/submit/file';
        Log::info("Cuckoo API URL: " . $url);

        return Http::withHeaders([
            'Authorization' => 'token ' . $this->apiKey,
        ])->attach(
            'file', file_get_contents($filePath), basename($filePath)
        )->post($url, [
            'settings' => json_encode($additionalSettings)
        ]);
    }


    // CuckooApiService.php

    public function getAnalysisResults($analysisId)
    {
        $response = Http::withHeaders([
            'Authorization' => 'token ' . $this->apiKey,
        ])->get($this->baseUrl . '/analysis/' . $analysisId);

        return $response->json();
    }


}
