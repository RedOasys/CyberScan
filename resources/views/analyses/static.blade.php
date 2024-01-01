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
                    } else if (tabName === 'static' && data['static'] && data['static'].pe) {
                        populateStaticTab(data['static'], tabPane);
                    } else {
                        createTabContent(data[tabName], tabPane);
                    }
                }

                content.appendChild(tabPane);
                first = false;
            });
        }

        function populateStaticTab(staticData, container) {
            const staticFields = ['peid_signatures', 'pe_imports', 'pe_exports', 'pe_sections', 'pe_resources', 'pe_imphash', 'pe_timestamp'];
            const table = document.createElement('table');
            table.classList.add('table', 'table-striped');
            const tbody = document.createElement('tbody');

            staticFields.forEach(field => {
                const row = document.createElement('tr');
                const keyCell = document.createElement('td');
                keyCell.textContent = field;
                const valueCell = document.createElement('td');

                if (['pe_imphash', 'pe_timestamp'].includes(field)) {
                    valueCell.textContent = staticData.pe[field];
                } else if (field === 'peid_signatures') {
                    valueCell.textContent = staticData.pe[field].join(', ');
                } else if (field === 'pe_exports') {
                    // Handling pe_exports as an array of objects
                    valueCell.textContent = staticData.pe[field].map(obj => Object.entries(obj).map(([k, v]) => `${k}: ${v}`).join(', ')).join('; ');
                } else if (staticData.pe[field]) {
                    const btn = document.createElement('button');
                    btn.className = 'btn btn-primary';
                    btn.textContent = 'View Details';
                    btn.setAttribute('data-bs-toggle', 'modal');
                    btn.setAttribute('data-bs-target', `#${field}Modal`);
                    valueCell.appendChild(btn);
                    createModal(field, staticData.pe[field], container);
                }

                row.appendChild(keyCell);
                row.appendChild(valueCell);
                tbody.appendChild(row);
            });

            table.appendChild(tbody);
            container.appendChild(table);
        }

        function createModal(id, data, container) {
            const modal = document.createElement('div');
            modal.className = 'modal fade';
            modal.id = `${id}Modal`;
            modal.setAttribute('tabindex', '-1');
            modal.setAttribute('aria-labelledby', `${id}ModalLabel`);
            modal.setAttribute('aria-hidden', 'true');

            const modalDialog = document.createElement('div');
            modalDialog.className = 'modal-dialog modal-xl'; // Use modal-xl for extra large modal
            modal.appendChild(modalDialog);

            const modalContent = document.createElement('div');
            modalContent.className = 'modal-content';
            modalDialog.appendChild(modalContent);

            const modalHeader = document.createElement('div');
            modalHeader.className = 'modal-header';
            modalContent.appendChild(modalHeader);

            const modalTitle = document.createElement('h5');
            modalTitle.className = 'modal-title';
            modalTitle.id = `${id}ModalLabel`;
            modalTitle.textContent = id;
            modalHeader.appendChild(modalTitle);

            const closeButton = document.createElement('button');
            closeButton.className = 'btn-close';
            closeButton.setAttribute('data-bs-dismiss', 'modal');
            closeButton.setAttribute('aria-label', 'Close');
            modalHeader.appendChild(closeButton);

            const modalBody = document.createElement('div');
            modalBody.className = 'modal-body';
            modalContent.appendChild(modalBody);

            // Create tab navigation
            const navTabs = document.createElement('ul');
            navTabs.className = 'nav nav-tabs';
            navTabs.id = 'dllTabs';
            modalBody.appendChild(navTabs);

            // Create tab content
            const tabContent = document.createElement('div');
            tabContent.className = 'tab-content';
            modalBody.appendChild(tabContent);

            if (id === 'pe_imports' && Array.isArray(data)) {
                data.forEach((dllImport, index) => {
                    // Tab for each DLL
                    const tab = document.createElement('li');
                    tab.className = 'nav-item';
                    const tabLink = document.createElement('a');
                    tabLink.className = 'nav-link' + (index === 0 ? ' active' : '');
                    tabLink.id = `tab-${index}`;
                    tabLink.setAttribute('data-bs-toggle', 'tab');
                    tabLink.setAttribute('href', `#dll-${index}`);
                    tabLink.textContent = dllImport.dll;
                    tab.appendChild(tabLink);
                    navTabs.appendChild(tab);

                    // Content for each DLL
                    const tabPane = document.createElement('div');
                    tabPane.className = 'tab-pane fade' + (index === 0 ? ' show active' : '');
                    tabPane.id = `dll-${index}`;

                    const importsList = document.createElement('ul');
                    dllImport.imports.forEach(importItem => {
                        const listItem = document.createElement('li');
                        listItem.textContent = `${importItem.name} (${importItem.address})`;
                        importsList.appendChild(listItem);
                    });
                    tabPane.appendChild(importsList);
                    tabContent.appendChild(tabPane);
                }   );}



            else if (id === 'pe_sections' && Array.isArray(data)) {
                data.forEach((section, index) => {
                    // Create a tab for each section
                    const tab = document.createElement('li');
                    tab.className = 'nav-item';
                    const tabLink = document.createElement('a');
                    tabLink.className = 'nav-link' + (index === 0 ? ' active' : '');
                    tabLink.id = `${id}-tab-${index}`;
                    tabLink.setAttribute('data-bs-toggle', 'tab');
                    tabLink.setAttribute('href', `#${id}-${index}`);
                    tabLink.textContent = section.name;
                    tab.appendChild(tabLink);
                    navTabs.appendChild(tab);

                    // Create content for each section
                    const tabPane = document.createElement('div');
                    tabPane.className = 'tab-pane fade' + (index === 0 ? ' show active' : '');
                    tabPane.id = `${id}-${index}`;

                    const sectionDetails = document.createElement('ul');
                    Object.entries(section).forEach(([key, value]) => {
                        const detailItem = document.createElement('li');
                        detailItem.textContent = `${key}: ${value}`;
                        sectionDetails.appendChild(detailItem);
                    });
                    tabPane.appendChild(sectionDetails);
                    tabContent.appendChild(tabPane);
                });
            } else if (id === 'pe_resources' && Array.isArray(data)) {
                data.forEach((resource, index) => {
                    // Create a tab for each resource
                    const tab = document.createElement('li');
                    tab.className = 'nav-item';
                    const tabLink = document.createElement('a');
                    tabLink.className = 'nav-link' + (index === 0 ? ' active' : '');
                    tabLink.id = `${id}-tab-${index}`;
                    tabLink.setAttribute('data-bs-toggle', 'tab');
                    tabLink.setAttribute('href', `#${id}-${index}`);
                    tabLink.textContent = resource.name;
                    tab.appendChild(tabLink);
                    navTabs.appendChild(tab);

                    // Create content for each resource
                    const tabPane = document.createElement('div');
                    tabPane.className = 'tab-pane fade' + (index === 0 ? ' show active' : '');
                    tabPane.id = `${id}-${index}`;

                    const resourceDetails = document.createElement('ul');
                    Object.entries(resource).forEach(([key, value]) => {
                        const detailItem = document.createElement('li');
                        detailItem.textContent = `${key}: ${value}`;
                        resourceDetails.appendChild(detailItem);
                    });
                    tabPane.appendChild(resourceDetails);
                    tabContent.appendChild(tabPane);
                });
            } else {
                // Default content for other data types
                const content = document.createElement('p');
                content.textContent = JSON.stringify(data, null, 2);
                tabContent.appendChild(content);
            }

            container.appendChild(modal);
        }
        function createDropdownContent(data, container, level = 0) {
            if (typeof data === 'object' && data !== null) {
                Object.keys(data).forEach((key, index) => {
                    // Create a button to toggle the dropdown
                    const button = document.createElement('button');
                    button.className = 'btn btn-info mb-1';
                    button.type = 'button';
                    button.setAttribute('data-bs-toggle', 'collapse');
                    button.setAttribute('data-bs-target', `#collapse${level}-${index}`);
                    button.setAttribute('aria-expanded', 'false');
                    button.style.marginLeft = `${level * 20}px`; // Indent nested levels
                    button.textContent = key;

                    // Create a div for the dropdown content
                    const dropdownContent = document.createElement('div');
                    dropdownContent.id = `collapse${level}-${index}`;
                    dropdownContent.className = 'collapse';

                    if (typeof data[key] === 'object' && data[key] !== null) {
                        createDropdownContent(data[key], dropdownContent, level + 1); // Recursive call for nested data
                    } else {
                        // Handle primitive data types
                        const value = document.createElement('span');
                        value.textContent = data[key];
                        dropdownContent.appendChild(value);
                    }

                    container.appendChild(button);
                    container.appendChild(dropdownContent);
                });
            } else {
                // Handle primitive data types at the root level
                const value = document.createElement('span');
                value.textContent = data;
                container.appendChild(value);
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

        if (analysisSelect.value) {
            const selectedData = analysisData.find(item => item.id == analysisSelect.value);
            if (selectedData) {
                populateFields(selectedData.data);
            }
        }
    </script>
@endsection
