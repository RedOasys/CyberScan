<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\FileUpload; // Assuming this is your model for file uploads

class DashboardController extends Controller
{
    public function dashboardData()
    {
        $baseUrl = env('CUCKOO_API_BASE_URL');
        $apiToken = env('CUCKOO_API_TOKEN');
        $headers = ['Authorization' => 'token ' . $apiToken];

        // Fetch analyses data from Cuckoo API
        $response = Http::withHeaders($headers)->get($baseUrl . '/analyses/');
        $data = $response->json();

        $analyzedSamples = 0;
        $queuedSamples = 0;

        if (isset($data['analyses'])) {
            foreach ($data['analyses'] as $analysis) {
                if ($analysis['state'] === 'finished') {
                    $analyzedSamples++;
                } elseif ($analysis['state'] === 'pending') {
                    $queuedSamples++;
                }
            }
        }

        // Count of uploaded samples
        $uploadedSamples = FileUpload::count();

        return response()->json([
            'uploadedSamples' => $uploadedSamples,
            'analyzedSamples' => $analyzedSamples,
            'queuedSamples' => $queuedSamples,
        ]);
    }
}
