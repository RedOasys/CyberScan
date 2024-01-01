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
        const analysisData = @json($analyses); // JSON data assignment

        function populateFields(data) {
            const tabs = document.getElementById('analysisTabs');
            const content = document.getElementById('tabContent');
            tabs.innerHTML = '';
            content.innerHTML = '';

            // Define the hierarchical structure
            const hierarchy = {
                "Info": ["analysis_id", "score", "category"],
                "target": ["pe", "dll", "imports", "name"], // Adjust this hierarchy as needed
                "static": ["pe", "pe_imports", "dll", "imports", "name"] // Adjust this hierarchy as needed
                // Add more categories and their subcategories as needed
            };

            // Create tabs and content based on the hierarchy
            for (const tabName in hierarchy) {
                if (hierarchy.hasOwnProperty(tabName)) {
                    const tab = document.createElement('li');
                    tab.className = 'nav-item';
                    const tabLink = document.createElement('a');
                    tabLink.className = 'nav-link';
                    tabLink.id = tabName + '-tab';
                    tabLink.href = '#' + tabName;
                    tabLink.setAttribute('data-toggle', 'tab');
                    tabLink.setAttribute('role', 'tab');
                    tabLink.textContent = tabName;
                    tab.appendChild(tabLink);
                    tabs.appendChild(tab);

                    const tabPane = document.createElement('div');
                    tabPane.className = 'tab-pane fade';
                    tabPane.id = tabName;
                    tabPane.setAttribute('role', 'tabpanel');
                    tabPane.setAttribute('aria-labelledby', tabName + '-tab');
                    content.appendChild(tabPane);

                    // Populate the content based on the hierarchy
                    let currentData = data;
                    for (const subcategory of hierarchy[tabName]) {
                        if (currentData.hasOwnProperty(subcategory)) {
                            currentData = currentData[subcategory];
                        } else {
                            currentData = null;
                            break;
                        }
                    }

                    if (currentData) {
                        createTabContent(currentData, tabPane);
                    }
                }
            }
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
                const tabs = document.getElementById('analysisTabs');
                const content = document.getElementById('tabContent');
                tabs.innerHTML = '';
                content.innerHTML = '';
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
