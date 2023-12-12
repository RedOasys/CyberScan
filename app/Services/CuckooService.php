<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

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

    // Add other methods to interact with different API endpoints as needed
}
