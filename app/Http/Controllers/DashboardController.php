<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\FileUpload; // Assuming this is your model for file uploads
use App\Models\Detection;

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
                } elseif ($analysis['state'] === 'pending_pre' | $analysis['state'] === 'tasks_pending') {
                    $queuedSamples++;
                }
            }
        }

        // Count of uploaded samples
        $uploadedSamples = FileUpload::count();
        $detectedMalwareCount = Detection::where('detected', true)
            ->where('certainty', '>=', 50)
            ->count();
        $totalCount = Detection::count(); // Assuming this is the total count you want to use

        $percentageDetected = ($detectedMalwareCount / $totalCount) * 100;

        return response()->json([
            'uploadedSamples' => $uploadedSamples,
            'analyzedSamples' => $analyzedSamples,
            'queuedSamples' => $queuedSamples,
            'percentageDetected' => $percentageDetected, // Add the percentage to the response
        ]);
    }
}
