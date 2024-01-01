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

    <!-- Add the dropdown for selecting names -->
    <div class="col-md-12">
        <label for="static_names">Select Name:</label>
        <select id="static_names" class="form-select mb-3">
            <option value="">Select a Name</option>
            @foreach($staticData as $name)
                <option value="{{ $name['name'] }}">{{ $name['name'] }}</option>
            @endforeach
        </select>
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
        const staticNamesSelect = document.getElementById('static_names');
        const preAnalysisFields = document.getElementById('preAnalysisFields');
        const analysisData = @json($analyses); // JSON data assignment
        const staticData = @json($staticData); // JSON data for static content

        function populateFields(data) {
            const tabs = document.getElementById('analysisTabs');
            const content = document.getElementById('tabContent');
            tabs.innerHTML = '';
            content.innerHTML = '';

            // Tabs to be displayed
            const requiredTabs = ['Info', 'target', 'static'];
            let first = true;

            requiredTabs.forEach(tabName => {
                // Create the tab
                const tab = document.createElement('li');
                tab.className = 'nav-item';
                const tabLink = document.createElement('a');
                tabLink.className = 'nav-link' + (first ? ' active' : '');
                tabLink.id = tabName + '-tab';
                tabLink.href = '#' + tabName;
                tabLink.setAttribute('data-toggle', 'tab');
                tabLink.setAttribute('role', 'tab');
                tabLink.textContent = tabName.charAt(0).toUpperCase() + tabName.slice(1);
                tab.appendChild(tabLink);
                tabs.appendChild(tab);

                // Create the tab pane
                const tabPane = document.createElement('div');
                tabPane.className = 'tab-pane fade' + (first ? ' show active' : '');
                tabPane.id = tabName;
                tabPane.setAttribute('role', 'tabpanel');
                tabPane.setAttribute('aria-labelledby', tabName + '-tab');

                if (tabName === 'Info') {
                    // Populate the Info tab
                    populateInfoTab(data, tabPane);
                } else if (tabName === 'static') {
                    // Populate the Static tab with default content
                    populateStaticTab(tabPane);
                } else if (data[tabName]) {
                    // Populate other tabs
                    if (tabName === 'target') {
                        populateTargetTab(data[tabName], tabPane);
                    } else {
                        createTabContent(data[tabName], tabPane);
                    }
                }

                content.appendChild(tabPane);
                first = false;
            });
        }

        function populateStaticTab(container) {
            const staticData = @json($staticData); // Assuming $staticData is available in your blade

            if (!staticData || typeof staticData !== 'object') {
                container.textContent = 'No static data available';
                return;
            }

            const table = document.createElement('table');
            table.classList.add('table', 'table-striped'); // Bootstrap classes for styling

            // Create table header
            const thead = document.createElement('thead');
            const headerRow = document.createElement('tr');
            const headerKey = document.createElement('th');
            headerKey.textContent = 'Key';
            const headerValue = document.createElement('th');
            headerValue.textContent = 'Value';
            headerRow.appendChild(headerKey);
            headerRow.appendChild(headerValue);
            thead.appendChild(headerRow);
            table.appendChild(thead);

            // Create table body
            const tbody = document.createElement('tbody');

            Object.keys(staticData).forEach(key => {
                const row = document.createElement('tr');
                const keyCell = document.createElement('td');
                keyCell.textContent = key;
                const valueCell = document.createElement('td');

                // Special handling for arrays and objects
                if (Array.isArray(staticData[key])) {
                    valueCell.textContent = staticData[key].map(item => JSON.stringify(item)).join(', ');
                } else if (typeof staticData[key] === 'object' && staticData[key] !== null) {
                    valueCell.textContent = JSON.stringify(staticData[key]);
                } else {
                    valueCell.textContent = staticData[key];
                }

                row.appendChild(keyCell);
                row.appendChild(valueCell);
                tbody.appendChild(row);
            });

            table.appendChild(tbody);
            container.appendChild(table);
        }

        function populateStaticTabContent(selectedName) {
            const container = document.getElementById('static');
            container.innerHTML = '';

            const selectedStaticData = staticData.find(item => item.name === selectedName);

            if (selectedStaticData) {
                populateStaticTab(selectedStaticData, container);
            } else {
                container.textContent = 'No data for selected name';
            }
        }


        function populateInfoTab(data, container) {
            const infoFields = ['analysis_id', 'score', 'category'];
            const table = document.createElement('table');
            table.classList.add('table', 'table-striped');
            const tbody = document.createElement('tbody');

            infoFields.forEach(field => {
                if (data.hasOwnProperty(field)) {
                    const row = document.createElement('tr');
                    const keyCell = document.createElement('td');
                    keyCell.textContent = field;
                    const valueCell = document.createElement('td');
                    valueCell.textContent = data[field];
                    row.appendChild(keyCell);
                    row.appendChild(valueCell);
                    tbody.appendChild(row);
                }
            });

            table.appendChild(tbody);
            container.appendChild(table);
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

        staticNamesSelect.addEventListener('change', function () {
            const selectedName = this.value;
            // Call a function to populate the Static tab based on the selected name
            populateStaticTabContent(selectedName);
        });

        if (analysisSelect.value) {
            const selectedData = analysisData.find(item => item.id == analysisSelect.value);
            if (selectedData) {
                populateFields(selectedData.data);
            }
        }
    </script>
@endsection
