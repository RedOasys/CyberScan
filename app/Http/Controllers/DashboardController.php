<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Http;
use App\Models\FileUpload; // Assuming this is your model for file uploads
use App\Models\Detection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

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
        // Group by a common prefix/category in the malware_type
        $groupedMalwareTypes = Detection::selectRaw("SUBSTRING_INDEX(malware_type, '.', 4) as category")
            ->where('malware_type', '!=', 'Unknown') // Exclude 'Unknown' malware types
            ->where('certainty', '>=', 50) // Include only detections with 50% or more certainty
            ->groupBy('category')
            ->get();

        // Count total detections with certainty 50% or more
        $totalDetections = Detection::where('certainty', '>=', 50)->count();
        $labels = [];
        $percentages = [];

        foreach ($groupedMalwareTypes as $groupedType) {
            $category = $groupedType->category;
            // Count detections per category with certainty 50% or more
            $count = Detection::where('malware_type', 'LIKE', $category . '%')
                ->where('certainty', '>=', 50)
                ->count();
            $percentage = ($totalDetections > 0) ? ($count / $totalDetections) * 100 : 0;

            $labels[] = $category;
            $percentages[] = round($percentage, 2);
        }

        return response()->json([
            'labels' => $labels,
            'percentages' => $percentages,
        ]);
    }

}
