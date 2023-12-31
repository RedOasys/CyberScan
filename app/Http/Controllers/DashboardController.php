<?php

namespace App\Http\Controllers;

use App\Models\StaticAnalysis;
use Illuminate\Support\Facades\Http;
use App\Models\FileUpload; // Assuming this is your model for file uploads
use App\Models\Detection;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function dashboardData()
    {
        // Count queued samples
        $queuedSamples = StaticAnalysis::whereIn('state', ['pending_pre', 'tasks_pending'])->count();


        // Count analyzed samples
        $analyzedSamples = StaticAnalysis::where('state', 'finished')->count();

        // Count uploaded samples
        $uploadedSamples = FileUpload::count();

        $detectedMalwareCount = Detection::where('detected', true)
            ->where('certainty', '>=', 50)
            ->count();

        $totalCount = Detection::count();

        $percentageDetected = ($detectedMalwareCount / $totalCount) * 100;

        return response()->json([
            'uploadedSamples' => $uploadedSamples,
            'analyzedSamples' => $analyzedSamples,
            'queuedSamples' => $queuedSamples,
            'percentageDetected' => $percentageDetected,
        ]);
    }
    public function malwareTypeDistribution()
    {
        // Group by a common prefix/category in the malware_type
        $groupedMalwareTypes = Detection::selectRaw("SUBSTRING_INDEX(malware_type, '.', 2) as category")
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
    public function malwareDetectionDistribution()
    {
        // Group by the source
        $groupedSources = Detection::select('source')
            ->where('certainty', '>=', 50) // Include only detections with 50% or more certainty
            ->groupBy('source')
            ->get();

        // Count total detections with certainty 50% or more
        $totalDetections = Detection::where('certainty', '>=', 50)->count();
        $labels = [];
        $percentages = [];

        foreach ($groupedSources as $source) {
            $sourceType = $source->source;

            // Count detections per source type with certainty 50% or more
            $count = Detection::where('source', $sourceType)
                ->where('certainty', '>=', 50)
                ->count();
            $percentage = ($totalDetections > 0) ? ($count / $totalDetections) * 100 : 0;

            $labels[] = $sourceType;
            $percentages[] = round($percentage, 2);
        }

        return response()->json([
            'labels' => $labels,
            'percentages' => $percentages,
        ]);
    }



}
