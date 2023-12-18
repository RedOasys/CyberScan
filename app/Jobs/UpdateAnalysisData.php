<?php

namespace App\Jobs;

use App\Models\StaticAnalysis;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class UpdateAnalysisData implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $analysis;

    public function __construct(StaticAnalysis $analysis)
    {
        $this->analysis = $analysis;
    }

    public function handle()
    {
        $response = Http::withHeaders([
            'Authorization' => 'token ' . env('CUCKOO_API_TOKEN'),
        ])->get(env('CUCKOO_API_BASE_URL') . '/analysis/' . $this->analysis->analysis_id);

        if ($response->successful()) {
            $analysisDetails = $response->json();
            $submitted = $analysisDetails['submitted'] ?? [];
            $fileUpload = FileUpload::where('md5_hash', $submitted['md5'] ?? '')->first();

            if ($fileUpload) {
                $updateData = [
                    'file_upload_id' => $fileUpload->id,
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

                $this->analysis->update($updateData);
                Log::info('Updated analysis data for ID ' . $this->analysis->analysis_id);
            } else {
                Log::error('Failed to find matching file upload for analysis ID: ' . $this->analysis->analysis_id);
            }
        } else {
            Log::error('Failed to fetch details for analysis ID: ' . $this->analysis->analysis_id . '. Response: ' . $response->body());
        }
    }
}
