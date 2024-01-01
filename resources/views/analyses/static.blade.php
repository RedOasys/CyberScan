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

            const topLevelTabs = ['Info', 'signatures', 'target', 'ttps', 'tags', 'families', 'platforms', 'virustotal', 'static', 'command', 'errors'];
            let first = true;

            topLevelTabs.forEach(tabName => {
                if (data[tabName] && Object.keys(data[tabName]).length !== 0) {
                    const tab = createTab(tabName, first);
                    tabs.appendChild(tab);

                    const tabPane = createTabPane(tabName, first);
                    if (tabName === 'Info') {
                        populateInfoTab(data, tabPane);
                    } else if (tabName === 'static') {
                        populateStaticTab(data[tabName], tabPane);
                    } else {
                        createGeneralTabContent(data[tabName], tabPane, tabName);
                    }
                    content.appendChild(tabPane);

                    first = false;
                }
            });
        }

        function createGeneralTabContent(data, container, tabName) {
            if (Array.isArray(data)) {
                data.forEach(item => createTabContent(item, container));
            } else if (typeof data === 'object' && data !== null) {
                for (const key in data) {
                    if (data.hasOwnProperty(key)) {
                        const subContainer = document.createElement('div');
                        subContainer.classList.add('mb-3');

                        const label = document.createElement('strong');
                        label.textContent = key + ': ';
                        subContainer.appendChild(label);

                        if (Array.isArray(data[key])) {
                            const list = document.createElement('ul');
                            data[key].forEach(item => {
                                const listItem = document.createElement('li');
                                createTabContent(item, listItem);
                                list.appendChild(listItem);
                            });
                            subContainer.appendChild(list);
                        } else if (typeof data[key] === 'object') {
                            const valueContainer = document.createElement('div');
                            createTabContent(data[key], valueContainer);
                            subContainer.appendChild(valueContainer);
                        } else {
                            const value = document.createElement('span');
                            value.textContent = data[key];
                            subContainer.appendChild(value);
                        }

                        container.appendChild(subContainer);
                    }
                }
            } else {
                const value = document.createElement('span');
                value.textContent = data;
                container.appendChild(value);
            }
        }

        function populateInfoTab(data, container) {
            ['analysis_id', 'score', 'category', 'sha256'].forEach(key => {
                if (data[key]) {
                    const field = document.createElement('p');
                    field.innerHTML = `<strong>${key}:</strong> ${data[key]}`;
                    container.appendChild(field);
                }
            });
        }
        function populateTargetTab(targetData, container) {
            // Create a table
            const table = document.createElement('table');
            table.classList.add('table');

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

                // Handle arrays and objects differently
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

        function populateStaticTab(staticData, container) {
            if (staticData.pe && staticData.pe.pe_imports) {
                const select = document.createElement('select');
                select.classList.add('form-select', 'mb-3');
                select.addEventListener('change', () => displayImportDetails(staticData.pe.pe_imports, select.value, container));

                staticData.pe.pe_imports.forEach((importItem, index) => {
                    const option = document.createElement('option');
                    option.value = index;
                    option.textContent = importItem.dll;
                    select.appendChild(option);
                });

                container.appendChild(select);
                displayImportDetails(staticData.pe.pe_imports, 0, container); // Display details for the first import by default
            }
        }

        function displayImportDetails(imports, selectedIndex, container) {
            const detailsContainer = document.getElementById('importDetails') || document.createElement('div');
            detailsContainer.id = 'importDetails';
            detailsContainer.innerHTML = ''; // Clear previous details

            const selectedImport = imports[selectedIndex];
            if (selectedImport) {
                // Create a table
                const table = document.createElement('table');
                table.classList.add('table');

                // Create table header
                const thead = document.createElement('thead');
                const headerRow = document.createElement('tr');
                const headerAddress = document.createElement('th');
                headerAddress.textContent = 'Address';
                const headerName = document.createElement('th');
                headerName.textContent = 'Name';
                headerRow.appendChild(headerAddress);
                headerRow.appendChild(headerName);
                thead.appendChild(headerRow);
                table.appendChild(thead);

                // Create table body
                const tbody = document.createElement('tbody');

                selectedImport.imports.forEach(importDetail => {
                    const row = document.createElement('tr');
                    const addressCell = document.createElement('td');
                    addressCell.textContent = importDetail.address;
                    const nameCell = document.createElement('td');
                    nameCell.textContent = importDetail.name;
                    row.appendChild(addressCell);
                    row.appendChild(nameCell);
                    tbody.appendChild(row);
                });

                table.appendChild(tbody);
                detailsContainer.appendChild(table);
            }

            container.appendChild(detailsContainer);
        }

        function createTab(tabName, isActive) {
            const tab = document.createElement('li');
            tab.className = 'nav-item';

            const tabLink = document.createElement('a');
            tabLink.className = 'nav-link' + (isActive ? ' active' : '');
            tabLink.id = `${tabName}-tab`;
            tabLink.href = `#${tabName}`;
            tabLink.setAttribute('data-toggle', 'tab');
            tabLink.setAttribute('role', 'tab');
            tabLink.textContent = tabName.charAt(0).toUpperCase() + tabName.slice(1);

            tab.appendChild(tabLink);

            return tab;
        }


        function createTabPane(tabName, isActive) {
            const tabPane = document.createElement('div');
            tabPane.className = 'tab-pane fade' + (isActive ? ' show active' : '');
            tabPane.id = tabName;
            tabPane.setAttribute('role', 'tabpanel');
            tabPane.setAttribute('aria-labelledby', `${tabName}-tab`);

            return tabPane;
        }

        function createTabContent(data, container) {
            if (Array.isArray(data)) {
                // Handle arrays
                const list = document.createElement('ul');
                list.classList.add('list-unstyled');
                data.forEach(item => {
                    const listItem = document.createElement('li');
                    createTabContent(item, listItem); // Recursive call for each item
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

                        if (typeof data[key] === 'object' && data[key] !== null) {
                            const valueContainer = document.createElement('div');
                            createTabContent(data[key], valueContainer); // Recursive call for nested objects
                            subContainer.appendChild(valueContainer);
                        } else {
                            // Handle primitive data types within an object
                            const value = document.createElement('span');
                            value.textContent = data[key];
                            subContainer.appendChild(value);
                        }

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
