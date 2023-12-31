<?php

namespace App\Http\Controllers;

use App\Models\PostAnalysis;
use App\Models\PreAnalysis;

class AnalysisDisplayController extends Controller
{
    public function static()
    {
        $analyses = PreAnalysis::all()->map(function ($analysis) {
            $analysis->parsed_data = json_decode($analysis->data, true);
            return $analysis;
        });

        // Create the analysisData array here and pass it to the view
        $analysisData = $analyses->map(function ($analysis) {
            return [
                'id' => $analysis->id,
                'analysis_id' => $analysis->parsed_data['analysis_id'],
                'score' => $analysis->parsed_data['score'],
                'category' => $analysis->parsed_data['category'],
                'target' => [
                    'filename' => $analysis->parsed_data['target']['filename'],
                    'orig_filename' => $analysis->parsed_data['target']['orig_filename'],
                    'platforms' => [
                        'platform' => $analysis->parsed_data['target']['platforms'][0]['platform'],
                        'os_version' => $analysis->parsed_data['target']['platforms'][0]['os_version'],
                    ],
                    'size' => $analysis->parsed_data['target']['size'],
                    'filetype' => $analysis->parsed_data['target']['filetype'],
                    'media_type' => $analysis->parsed_data['target']['media_type'],
                    'sha256' => $analysis->parsed_data['target']['sha256'],
                    'sha1' => $analysis->parsed_data['target']['sha1'],
                    'md5' => $analysis->parsed_data['target']['md5'],
                ],
                'static' => [
                    'pe' => [
                        'peid_signatures' => $analysis->parsed_data['static']['pe']['peid_signatures'],
                        'pe_imports' => $analysis->parsed_data['static']['pe']['pe_imports'],
                        'pe_exports' => $analysis->parsed_data['static']['pe']['pe_exports'],
                        'pe_sections' => $analysis->parsed_data['static']['pe']['pe_sections'],
                        'pe_resources' => $analysis->parsed_data['static']['pe']['pe_resources'],
                        'pe_versioninfo' => $analysis->parsed_data['static']['pe']['pe_versioninfo'],
                        'pe_imphash' => $analysis->parsed_data['static']['pe']['pe_imphash'],
                        'pe_timestamp' => $analysis->parsed_data['static']['pe']['pe_timestamp'],
                    ],
                ],
            ];
        });

        return view('analyses.static', compact('analyses', 'analysisData'));
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
