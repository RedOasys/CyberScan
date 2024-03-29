<div class="btn-group dropstart">
    <button type="button" class="btn btn-outline-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
        Actions
    </button>
    <ul class="dropdown-menu">
        <!-- PreAnalysis Info -->
        <li>
            <a class="dropdown-item" href="{{ route('analysis.tasks.result', ['analysisId' => $analysis->analysis_id]) }}">
                Pre Analysis Info
            </a>
        </li>
        <!-- Placeholder for PostAnalysis Info -->
        <li>


            <a class="dropdown-item" href="{{ route('analysis.dynamic')}}">Dynamic Analysis Info</a>


        </li>
        <!-- Virustotal Info -->
        <li>
            @if(!empty($analysis->md5))
                <a class="dropdown-item" href="{{ route('analysis.virustotal', ['md5' => $analysis->md5]) }}">
                    Virustotal Info
                </a>
            @else
                <span class="dropdown-item text-muted">No Actions</span>
            @endif
        </li>
    </ul>
</div>
