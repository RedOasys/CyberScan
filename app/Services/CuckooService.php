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



    // Add other methods to interact with different API endpoints as needed
}
