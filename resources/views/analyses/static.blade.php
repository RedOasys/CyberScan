@extends('layouts.chips.main')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Dropdown to Select Analysis -->
            <label for="analysis_id">Select Analysis:</label>
            <select id="analysis_id" class="form-select mb-3">
                <option value="">Select an Analysis</option>
                @foreach($analyses as $analysis)
                    <option value="{{ $analysis['id'] }}">{{ $analysis['analysis_id'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">
                    PreAnalysis Information
                </h2>
            </div>
            <div id="preAnalysisFields" class="card-body">
                <!-- Content will be dynamically generated here -->
            </div>
        </div>
    </div>

    <!-- Include Bootstrap and jQuery scripts for dynamic content handling -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
            crossorigin="anonymous"></script>

    <script>
        const analysisSelect = document.getElementById('analysis_id');
        const preAnalysisFields = document.getElementById('preAnalysisFields');
        const analysisData = @json($analyses); // JSON data assignment


        function populateFields(data) {
            // Clear existing content
            preAnalysisFields.innerHTML = '';

            // Iterate over the data object and create UI elements for each field
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    const value = data[key];
                    const fieldContainer = document.createElement('div');
                    fieldContainer.classList.add('mb-3');

                    const fieldTitle = document.createElement('h5');
                    fieldTitle.textContent = key.charAt(0).toUpperCase() + key.slice(1);

                    const fieldValue = document.createElement('p');
                    fieldValue.textContent = typeof value === 'object' ? JSON.stringify(value, null, 2) : value;

                    fieldContainer.appendChild(fieldTitle);
                    fieldContainer.appendChild(fieldValue);
                    preAnalysisFields.appendChild(fieldContainer);
                }
            }
        }

        analysisSelect.addEventListener('change', function () {
            const selectedId = this.value;
            const selectedData = analysisData.find(item => item.id == selectedId);
            if (selectedData) {
                populateFields(selectedData.data);
            } else {
                preAnalysisFields.innerHTML = '';
            }
        });

        // Initialize fields if an analysis is already selected
        if (analysisSelect.value) {
            const selectedData = analysisData.find(item => item.id == analysisSelect.value);
            if (selectedData) {
                populateFields(selectedData.data);
            }
        }
    </script>
@endsection
