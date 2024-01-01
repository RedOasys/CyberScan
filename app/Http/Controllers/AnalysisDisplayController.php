<?php

namespace App\Http\Controllers;

use App\Models\PostAnalysis;
use App\Models\PreAnalysis;

class AnalysisDisplayController extends Controller
{
    public function Static()
    {
        // Retrieve all PreAnalysis records and decode their JSON data
        $preAnalyses = PreAnalysis::all()->map(function ($analysis) {
            $analysis->parsed_data = json_decode($analysis->data, true);
            return $analysis;
        });

        // Create the analysisData array
        $analysisData = $preAnalyses->map(function ($analysis) {
            // Determine the analysis_id; use task_id if analysis_id is not set
            $analysisId = $analysis->parsed_data['analysis_id'] ?? str_replace('_1', '', $analysis->parsed_data['task_id']);

            return [
                'id' => $analysis->id,
                'analysis_id' => $analysisId,
                'data' => $analysis->parsed_data,
            ];
        });

        // Pass the preAnalyses and analysisData to the view
        return view('pre_analyses.display', compact('preAnalyses', 'analysisData'));
    }

    public function dynamic()
    {
        $analyses = PostAnalysis::all()->map(function ($analysis) {
            $analysis->parsed_data = json_decode($analysis->data, true);
            return $analysis;
        });

        // Create the analysisData array here and pass it to the view
        $analysisData = $analyses->map(function ($analysis) {
            $analysisId = isset($analysis->parsed_data['analysis_id']) ? $analysis->parsed_data['analysis_id'] : str_replace('_1', '', $analysis->parsed_data['task_id']);

            return [
                'id' => $analysis->id,
                'analysis_id' => $analysisId,
                'data' => $analysis->parsed_data,
            ];
        });

        // Pass the analyses and analysisData to the view
        return view('analyses.dynamic', compact('analyses', 'analysisData'));
    }

}
