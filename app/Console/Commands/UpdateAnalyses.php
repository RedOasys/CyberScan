<?php

namespace App\Console\Commands;

use App\Jobs\UpdateAnalysisData;
use App\Models\StaticAnalysis;
use Illuminate\Console\Command;

class UpdateAnalyses extends Command
{
    protected $signature = 'update:analyses';
    protected $description = 'Update analyses data from API';

    public function handle()
    {
        $this->info('Starting update of analyses data...');
        $analyses = StaticAnalysis::all();

        foreach ($analyses as $analysis) {
            UpdateAnalysisData::dispatch($analysis);
        }

        $this->info('All analysis update jobs dispatched.');
    }
}
