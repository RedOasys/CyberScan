@extends('layouts.chips.main')

@section('content')
    <div class="row">
        <div class="col-md-12">
            <!-- Dropdown to Select Analysis -->
            <label for="analysis_id">Select Analysis:</label>
            <select id="analysis_id" class="form-select mb-3">
                <option value="">Select an Analysis</option>
                @foreach($analyses as $analysis)
                    <option value="{{ $analysis->id }}">{{ $analysis->parsed_data['task_id'] }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-12">
        <div class="card mb-5">
            <div class="card-header bg-primary text-white">
                <h2 class="mb-0">
                    Dynamic Analysis Information
                </h2>
            </div>
            <div id="dynamicFields" class="card-body">
                <!-- Fields will be dynamically generated here -->
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
        const dynamicFields = document.getElementById('dynamicFields');
        const analysisData = {!! $analysisData->toJson() !!}; // Use $analysisData here

        function generateFields(data) {
            const initialFields = ['task_id', 'score', 'tags', 'families'];
            const collapsibleFields = {};

            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    if (initialFields.includes(key)) {
                        if (data[key] !== null && data[key] !== "" && Object.keys(data[key]).length !== 0) { // Skip empty or null fields
                            const listItem = document.createElement('li');
                            const strong = document.createElement('strong');
                            strong.textContent = key + ': ';
                            listItem.appendChild(strong);

                            if (typeof data[key] === 'object') {
                                const ul = document.createElement('ul');
                                ul.classList.add('list-unstyled');
                                listItem.appendChild(ul);
                                generateFieldsRecursive(data[key], ul);
                            } else {
                                const span = document.createElement('span');
                                span.textContent = data[key];
                                listItem.appendChild(span);
                            }

                            dynamicFields.appendChild(listItem);
                        }
                    } else {
                        if (data[key] !== null && data[key] !== "" && Object.keys(data[key]).length !== 0) { // Skip empty or null fields
                            collapsibleFields[key] = data[key];
                        }
                    }
                }
            }

            for (const key in collapsibleFields) {
                if (collapsibleFields.hasOwnProperty(key)) {
                    const group = document.createElement('div');
                    group.classList.add('mb-3');

                    const button = document.createElement('button');
                    button.classList.add('btn', 'btn-secondary', 'dropdown-toggle');
                    button.setAttribute('type', 'button');
                    button.setAttribute('data-toggle', 'collapse');
                    button.setAttribute('data-target', `#${key}`);
                    button.textContent = key;

                    group.appendChild(button);

                    const groupContent = document.createElement('div');
                    groupContent.classList.add('collapse');
                    groupContent.id = key;

                    const ul = document.createElement('ul');
                    ul.classList.add('list-unstyled');

                    generateFieldsRecursive(collapsibleFields[key], ul);

                    groupContent.appendChild(ul);
                    group.appendChild(groupContent);
                    dynamicFields.appendChild(group);
                }
            }
        }

        function generateFieldsRecursive(data, parent) {
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    if (data[key] !== null && data[key] !== "" && Object.keys(data[key]).length !== 0) { // Skip empty or null fields
                        const listItem = document.createElement('li');
                        const strong = document.createElement('strong');
                        strong.textContent = key + ': ';
                        listItem.appendChild(strong);

                        if (typeof data[key] === 'object') {
                            const ul = document.createElement('ul');
                            ul.classList.add('list-unstyled');
                            listItem.appendChild(ul);
                            generateFieldsRecursive(data[key], ul);
                        } else {
                            const span = document.createElement('span');
                            span.textContent = data[key];
                            listItem.appendChild(span);
                        }

                        parent.appendChild(listItem);
                    }
                }
            }
        }

        function clearDynamicFields() {
            dynamicFields.innerHTML = ''; // Clear existing fields
        }

        function handlePopulateCards() {
            const selectedId = analysisSelect.value;
            const selectedData = analysisData.find(item => item.id == selectedId);
            if (selectedData) {
                clearDynamicFields(); // Clear existing fields
                generateFields(selectedData.data);
            } else {
                // Clear the fields if no data found for the selected analysis
                clearDynamicFields();
            }
        }

        document.addEventListener('DOMContentLoaded', function () {
            analysisSelect.addEventListener('change', handlePopulateCards);
        });

        // Populate card fields with data for the initial selected analysis (if any)
        if (analysisSelect.value) {
            handlePopulateCards();
        }
    </script>
@endsection
