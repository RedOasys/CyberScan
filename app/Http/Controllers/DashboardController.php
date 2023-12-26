<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\FileUpload; // Assuming this is your model for file uploads
use App\Models\Detection;
use Illuminate\Http\Request;


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
    public function malwareTypeDistribution()
    {
        // Query your database to get the malware type distribution data
        $malwareTypes = Detection::select('malware_type')
            ->groupBy('malware_type')
            ->get();

        $labels = [];
        $percentages = [];

        // Calculate the count and percentages for each malware type
        foreach ($malwareTypes as $malwareType) {
            $label = $malwareType->malware_type;
            $count = Detection::where('malware_type', $label)->count();
            $percentage = ($count / $malwareTypes->count()) * 100;

            $labels[] = $label;
            $percentages[] = round($percentage, 2); // Round to 2 decimal places
        }

        $data = [
            'labels' => $labels,
            'percentages' => $percentages,
        ];

        return response()->json($data);
    }

}
