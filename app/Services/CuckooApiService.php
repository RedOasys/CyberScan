<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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
        $response = Http::withHeaders([
            'Authorization' => 'token ' . $this->apiKey,
        ])->post($this->baseUrl . '/submit/file', [
            'file' => $filePath,
            'settings' => json_encode($additionalSettings),
        ]);

        return $response->json();
    }

}
