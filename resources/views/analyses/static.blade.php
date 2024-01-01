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
                <h2 class="mb-0">PreAnalysis Information</h2>
            </div>
            <div class="card-body">
                <ul class="nav nav-tabs" id="analysisTabs"></ul> <!-- Tabs -->
                <div class="tab-content" id="tabContent"></div> <!-- Tab Content -->
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
            const tabs = document.getElementById('analysisTabs');
            const content = document.getElementById('tabContent');
            tabs.innerHTML = '';
            content.innerHTML = '';

            let first = true;
            for (const key in data) {
                if (data.hasOwnProperty(key)) {
                    // Create the tab
                    const tab = document.createElement('li');
                    tab.className = 'nav-item';
                    const tabLink = document.createElement('a');
                    tabLink.className = 'nav-link' + (first ? ' active' : '');
                    tabLink.id = key + '-tab';
                    tabLink.href = '#' + key;
                    tabLink.setAttribute('data-toggle', 'tab');
                    tabLink.setAttribute('role', 'tab');
                    tabLink.textContent = key.charAt(0).toUpperCase() + key.slice(1);
                    tab.appendChild(tabLink);
                    tabs.appendChild(tab);

                    // Create the tab pane
                    const tabPane = document.createElement('div');
                    tabPane.className = 'tab-pane fade' + (first ? ' show active' : '');
                    tabPane.id = key;
                    tabPane.setAttribute('role', 'tabpanel');
                    tabPane.setAttribute('aria-labelledby', key + '-tab');

                    // Special handling for the 'target' tab
                    if (key === 'target') {
                        populateTargetTab(data[key], tabPane);
                    } else {
                        // General handling for other tabs
                        createTabContent(data[key], tabPane);
                    }

                    content.appendChild(tabPane);
                    first = false;
                }
            }
        }

        function populateTargetTab(targetData, container) {
            const table = document.createElement('table');
            table.classList.add('table', 'table-striped'); // Bootstrap classes for styling

            // Create table header
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            const headerKey = document.createElement('th');
            headerKey.textContent = 'Info';
            const headerValue = document.createElement('th');
            headerValue.textContent = 'Value';
            headerRow.appendChild(headerKey);
            headerRow.appendChild(headerValue);
            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Create table body
            const tbody = document.createElement('tbody');

            Object.keys(targetData).forEach(key => {
                const row = document.createElement('tr');
                const keyCell = document.createElement('td');
                keyCell.textContent = key;
                const valueCell = document.createElement('td');

                // Special handling for arrays and objects
                if (Array.isArray(targetData[key])) {
                    valueCell.textContent = targetData[key].map(item => JSON.stringify(item)).join(', ');
                } else if (typeof targetData[key] === 'object' && targetData[key] !== null) {
                    valueCell.textContent = JSON.stringify(targetData[key]);
                } else {
                    valueCell.textContent = targetData[key];
                }

                row.appendChild(keyCell);
                row.appendChild(valueCell);
                tbody.appendChild(row);
            });

            table.appendChild(tbody);
            container.appendChild(table);
        }

        function createTabContent(data, container) {
            if (Array.isArray(data)) {
                // Handle arrays
                const list = document.createElement('ul');
                list.classList.add('list-unstyled');
                data.forEach(item => {
                    const listItem = document.createElement('li');
                    createTabContent(item, listItem); // Recursive call
                    list.appendChild(listItem);
                });
                container.appendChild(list);
            } else if (typeof data === 'object' && data !== null) {
                // Handle objects
                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
                        const subContainer = document.createElement('div');
                        subContainer.classList.add('mb-3');

                        const label = document.createElement('strong');
                        label.textContent = key + ': ';
                        subContainer.appendChild(label);

                        const valueContainer = document.createElement('div');
                        createTabContent(data[key], valueContainer); // Recursive call
                        subContainer.appendChild(valueContainer);

                        container.appendChild(subContainer);
                    }
                }
            } else {
                // Handle primitive data types
                const value = document.createElement('span');
                value.textContent = data;
                container.appendChild(value);
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
