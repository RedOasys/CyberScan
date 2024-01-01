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
            $parsedData = $analysis->parsed_data;

            $targetPlatform = isset($parsedData['target']['platforms'][0])
                ? [
                    'platform' => $parsedData['target']['platforms'][0]['platform'],
                    'os_version' => $parsedData['target']['platforms'][0]['os_version'],
                ]
                : [
                    'platform' => null,
                    'os_version' => null,
                ];

            return [
                'id' => $analysis->id,
                'analysis_id' => $parsedData['analysis_id'] ?? null,
                'score' => $parsedData['score'] ?? null,
                'category' => $parsedData['category'] ?? null,
                'target' => [
                    'filename' => $parsedData['target']['filename'] ?? null,
                    'orig_filename' => $parsedData['target']['orig_filename'] ?? null,
                    'platforms' => [
                        $targetPlatform,
                    ],
                    'size' => $parsedData['target']['size'] ?? null,
                    'filetype' => $parsedData['target']['filetype'] ?? null,
                    'media_type' => $parsedData['target']['media_type'] ?? null,
                    'sha256' => $parsedData['target']['sha256'] ?? null,
                    'sha1' => $parsedData['target']['sha1'] ?? null,
                    'md5' => $parsedData['target']['md5'] ?? null,
                ],
                'static' => [
                    'pe' => [
                        'peid_signatures' => $parsedData['static']['pe']['peid_signatures'] ?? null,
                        'pe_imports' => $parsedData['static']['pe']['pe_imports'] ?? null,
                        'pe_exports' => $parsedData['static']['pe']['pe_exports'] ?? null,
                        'pe_sections' => $parsedData['static']['pe']['pe_sections'] ?? null,
                        'pe_resources' => $parsedData['static']['pe']['pe_resources'] ?? null,
                        'pe_versioninfo' => $parsedData['static']['pe']['pe_versioninfo'] ?? null,
                        'pe_imphash' => $parsedData['static']['pe']['pe_imphash'] ?? null,
                        'pe_timestamp' => $parsedData['static']['pe']['pe_timestamp'] ?? null,
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
