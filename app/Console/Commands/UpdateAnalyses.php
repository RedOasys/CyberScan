<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FileUpload;
use App\Models\StaticAnalysis;
use Illuminate\Support\Facades\Http;

class UpdateAnalyses extends Command
{
    protected $signature = 'update:analyses';
    protected $description = 'Update analyses data from API';

    public function __construct()
    {
        parent::__construct();
    }

    public function handle()
    {
        $this->info('Starting update of analyses data...');
        $this->updateAnalysisDetails();
        $this->info('Analyses data update completed.');
    }

    protected function updateAnalysisDetails()
    {
        $staticAnalyses = StaticAnalysis::all();

        foreach ($staticAnalyses as $analysis) {
            $response = Http::withHeaders([
                'Authorization' => 'token ' . env('CUCKOO_API_TOKEN'),
            ])->get(env('CUCKOO_API_BASE_URL') . '/analysis/' . $analysis->analysis_id);

            if ($response->successful()) {
                $analysisDetails = $response->json();

                // Update the analysis record with new details
                $analysis->update([
                    'state' => $analysisDetails['state'] ?? null,
                    'score' => $analysisDetails['score'] ?? 0,
                    'kind' => $analysisDetails['kind'] ?? null,
                    // ... other fields ...
                ]);

                $this->info("Updated analysis: " . $analysis->analysis_id);
            } else {
                $this->error("Failed to fetch details for analysis: " . $analysis->analysis_id);
            }
        }
    }

    // Additional methods for other types of analyses can be added here
}
