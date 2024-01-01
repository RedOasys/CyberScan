<?php

namespace App\Http\Controllers;

use App\Models\PostAnalysis;
use App\Models\PreAnalysis;

class AnalysisDisplayController extends Controller
{
    public function Static()
    {
        $analyses = PreAnalysis::all()->map(function ($analysis) {
            $analysis->parsed_data = json_decode($analysis->data, true);
            return [
                'id' => $analysis->id,
                'analysis_id' => $analysis->parsed_data['analysis_id'] ?? str_replace('_1', '', $analysis->parsed_data['task_id']),
                'data' => $analysis->parsed_data,
            ];
        });

        return view('analyses.static', compact('analyses'));
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
