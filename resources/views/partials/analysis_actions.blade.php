{{-- File: resources/views/partials/analysis_actions.blade.php --}}
<div class="btn-group dropstart">
    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        Actions
    </button>
    <ul class="dropdown-menu">
        <!-- PreAnalysis Info -->
        <li>
            <a class="dropdown-item" href="{{ route('analysis.tasks.result', ['analysisId' => $analysis->analysis_id]) }}">
                PreAnalysis Info
            </a>
        </li>
        <!-- Placeholder for PostAnalysis Info -->
        <li><a class="dropdown-item" href="#">PostAnalysis Info</a></li>
        <!-- Virustotal Info -->
        <li>
            <a class="dropdown-item" href="{{ route('analysis.virustotal', ['md5' => $analysis->md5]) }}">
                Virustotal Info
            </a>
        </li>
    </ul>
</div>
