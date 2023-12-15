<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\FileUpload;
use App\Models\StaticAnalysis;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

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

                // Assuming that the analysis details contain a 'submitted' section with an 'md5' hash
                $submitted = $analysisDetails['submitted'] ?? [];
                $fileUpload = FileUpload::where('md5_hash', $submitted['md5'] ?? '')->first();

                if ($fileUpload) {
                    // Map the extracted data
                    $updateData = [
                        'file_upload_id' => $fileUpload->id, // Associate with the correct file upload ID
                        'analysis_id' => $analysisDetails['id'] ?? null,
                        'score' => $analysisDetails['score'] ?? 0,
                        'kind' => $analysisDetails['kind'] ?? null,
                        'state' => $analysisDetails['state'] ?? null,
                        'media_type' => $submitted['media_type'] ?? null,
                        'md5' => $submitted['md5'] ?? null,
                        'sha1' => $submitted['sha1'] ?? null,
                        'sha256' => $submitted['sha256'] ?? null,
                        'created_at' => $analysisDetails['created_on'] ?? null,
                        'updated_at' => now(),
                    ];
                    Log::info('API Response for analysis ' . $analysis->analysis_id . ': ', $analysisDetails);
                    $analysis->update($updateData);

                    $this->info("Updated analysis: " . $analysis->analysis_id);
                } else {
                    $this->error("Failed to find matching file upload for analysis: " . $analysis->analysis_id);
                }
            } else {
                $this->error("Failed to fetch details for analysis: " . $analysis->analysis_id);

            }

        }
    }

    // Additional methods for other types of analyses can be added here
}
