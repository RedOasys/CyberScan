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
            <div class="col-md-12">
                <div class="card mb-5">
                    <div class="card-header bg-primary text-white">
                        <h2 class="mb-0">
                            <button class="btn btn-link text-white" data-toggle="collapse" data-target="#basicInfo">
                                Static Analysis Information
                            </button>
                        </h2>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled">
                            <li><strong>Analysis ID:</strong> <span id="analysis_id_value"></span></li>
                            <!-- Remove the other card fields here -->
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap and jQuery scripts for the collapse functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>

    <script>
        const analysisSelect = document.getElementById('analysis_id');

        function handlePopulateCards() {
            const selectedId = analysisSelect.value;
            document.getElementById("analysis_id_value").textContent = selectedId;
            // You can add logic to populate other card fields here if needed
        }

        document.addEventListener('DOMContentLoaded', function () {
            analysisSelect.addEventListener('change', handlePopulateCards);
        });

        // Call the function to populate the card fields with data for the selected analysis
        analysisSelect.addEventListener('change', function () {
            handlePopulateCards();
        });

        // Populate card fields with data for the initial selected analysis (if any)
        if (analysisSelect.value) {
            handlePopulateCards();
        }
    </script>
@endsection
