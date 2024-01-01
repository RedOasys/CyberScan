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

    <!-- Include Bootstrap and jQuery scripts for the collapse functionality -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>

    <script>
        const analysisSelect = document.getElementById('analysis_id');

        function handlePopulateCards() {
            const selectedId = analysisSelect.value;
            // You can add your logic here to populate the card fields based on the selected ID
            // Example: fetch data from an API and update the card fields
        }

        // Add an event listener to handle changes in the dropdown selection
        analysisSelect.addEventListener('change', handlePopulateCards);

        // Call the function to populate the card fields for the initial selected analysis (if any)
        handlePopulateCards();
    </script>

@endsection
