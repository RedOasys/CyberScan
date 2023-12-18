{{-- File: resources/views/partials/analysis_actions.blade.php --}}
<div class="dropdown">
    <button class="btn btn-primary dropdown-toggle" type="button" id="dropdownMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
        Details
    </button>
    <div class="dropdown-menu" aria-labelledby="dropdownMenuButton">
        <a class="dropdown-item" href="{{ route('analysis.tasks.result', ['analysisId' => $analysis->analysis_id]) }}">PreAnalysis Info</a>
        {{-- Placeholder for PostAnalysis Info --}}
        <a class="dropdown-item" href="#">PostAnalysis Info</a>
        <a class="dropdown-item" href="{{ route('analysis.virustotal', ['md5' => $analysis->md5]) }}">Virustotal Info</a>
    </div>
</div>
