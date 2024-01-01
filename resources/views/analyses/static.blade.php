@extends('layouts.chips.main')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Dropdown to Select Analysis -->
            <label for="analysis_id">Select Analysis:</label>
            <select id="analysis_id" class="form-select mb-3">
                <option value="">Select an Analysis</option>

                @foreach($analyses as $analysis)
                    @if(!empty($analysis->name))
                        <option value="{{ $analysis->id }}">{{ $analysis->name }}</option>
                    @endif
                @endforeach
            </select>
        </div>
    </div>

    <!-- Include Bootstrap and jQuery scripts for the dropdown functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>

    <script>
        const analysisSelect = document.getElementById('analysis_id');

        // An array to store Analysis IDs
        const analysisData = [
                @foreach($analyses as $analysis)
            {
                id: '{{ $analysis->id }}',
                analysis_id: @if(isset($analysis->parsed_data['analysis_id']) && is_string($analysis->parsed_data['analysis_id'])) '{{ htmlspecialchars($analysis->parsed_data['analysis_id']) }}' @else '' @endif,
            },
            @endforeach
        ];

        function populateAnalysisDropdown() {
            // Add options to the analysis select dropdown
            analysisData.forEach(item => {
                const option = document.createElement('option');
                option.value = item.id;
                option.textContent = item.analysis_id; // Display the appropriate text
                analysisSelect.appendChild(option);
            });
        }

        document.addEventListener('DOMContentLoaded', function () {
            populateAnalysisDropdown();
        });
    </script>
@endsection
