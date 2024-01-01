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
        function populateStaticTab(staticData, container) {
            // Define the categories and map them to data
            const mainCategories = {
                'PE Signatures': staticData.pe?.peid_signatures,
                'PE Imports': staticData.pe?.pe_imports,
                'PE Exports': staticData.pe?.pe_exports,
                'PE Sections': staticData.pe?.pe_sections,
                'PE Resources': staticData.pe?.pe_resources,
                'PE VersionInfo': staticData.pe?.pe_versioninfo,
                'Other': {
                    'pe_imphash': staticData.pe?.pe_imphash,
                    'pe_timestamp': staticData.pe?.pe_timestamp,
                    'signatures': staticData.signatures
                }
            };

            // Create dropdown for main categories
            const select = document.createElement('select');
            select.classList.add('form-select', 'mb-3');
            select.addEventListener('change', () => displayStaticDetails(mainCategories, select.value, container));

            // Populate dropdown with main categories
            for (const category in mainCategories) {
                if (mainCategories[category]) {
                    const option = document.createElement('option');
                    option.value = category;
                    option.textContent = category;
                    select.appendChild(option);
                }
            }

            container.appendChild(select);
            displayStaticDetails(mainCategories, Object.keys(mainCategories)[0], container); // Display first category by default
        }

        function displayStaticDetails(categories, selectedCategory, container) {
            const detailsContainer = document.getElementById('staticDetails') || document.createElement('div');
            detailsContainer.id = 'staticDetails';
            detailsContainer.innerHTML = '';

            const data = categories[selectedCategory];
            if (data) {
                // Create and populate table based on selected category
                const table = createDataTable(data);
                detailsContainer.appendChild(table);
            }

            container.appendChild(detailsContainer);
        }
        function createDataTable(data) {
            const table = document.createElement('table');
            table.classList.add('table', 'table-striped');
            const thead = document.createElement('thead');
            const tbody = document.createElement('tbody');

            // Check if data is an array of objects
            if (Array.isArray(data) && data.length > 0 && typeof data[0] === 'object') {
                // Assuming the first item represents the structure
                const headers = Object.keys(data[0]);
                const tr = document.createElement('tr');
                headers.forEach(header => {
                    const th = document.createElement('th');
                    th.textContent = header;
                    tr.appendChild(th);
                });
                thead.appendChild(tr);

                data.forEach(item => {
                    const row = document.createElement('tr');
                    headers.forEach(header => {
                        const td = document.createElement('td');
                        td.textContent = item[header];
                        row.appendChild(td);
                    });
                    tbody.appendChild(row);
                });
            } else if (typeof data === 'object' && data !== null) {
                // Handle object data
                const tr = document.createElement('tr');
                tr.innerHTML = '<th>Key</th><th>Value</th>';
                thead.appendChild(tr);

                Object.keys(data).forEach(key => {
                    const row = document.createElement('tr');
                    const tdKey = document.createElement('td');
                    tdKey.textContent = key;
                    const tdValue = document.createElement('td');
                    tdValue.textContent = JSON.stringify(data[key]);
                    row.appendChild(tdKey);
                    row.appendChild(tdValue);
                    tbody.appendChild(row);
                });
            }

            table.appendChild(thead);
            table.appendChild(tbody);
            return table;
        }
        function populateDropdowns(staticData, container) {

            const peImports = staticData.pe?.pe_imports;
            const dllSelect = document.createElement('select');
            dllSelect.classList.add('form-select', 'mb-3');

            peImports.forEach(importEntry => {
                const option = document.createElement('option');
                option.value = importEntry.dll;
                option.textContent = importEntry.dll;
                dllSelect.appendChild(option);
            });

            dllSelect.addEventListener('change', () => {
                const selectedDll = dllSelect.value;
                const importsData = peImports.find(entry => entry.dll === selectedDll)?.imports;
                displayDetails(importsData, container);
            });

            container.appendChild(dllSelect);
            displayDetails(peImports[0].imports, container); // Default to first DLL's imports
        }
        function displayDetails(data, container) {
            container.innerHTML = ''; // Clear previous details
            if (!data) return;

            const table = createDataTable(data); // Reuse the createDataTable function
            container.appendChild(table);
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

        if (analysisSelect.value) {
            const selectedData = analysisData.find(item => item.id == analysisSelect.value);
            if (selectedData) {
                populateFields(selectedData.data);
            }
        }
    </script>
@endsection
