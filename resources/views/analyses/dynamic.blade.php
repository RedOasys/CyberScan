@extends('layouts.chips.main')

@section('content')
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.3/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
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
                <!-- Tab Navigation -->
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    <!-- Tabs will be dynamically generated here -->
                </ul>

                <!-- Tab Content -->
                <div class="tab-content" id="myTabContent">
                    <!-- Content will be dynamically generated here -->
                </div>
            </div>
        </div>
    </div>

    <!-- Include Bootstrap and jQuery scripts for the tab functionality -->
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
        function generateTabs(data, task_id) {            // Clear existing tabs and content
            const tabs = document.createElement('ul');
            tabs.classList.add('nav', 'nav-tabs');
            tabs.setAttribute('role', 'tablist');

            const content = document.createElement('div');
            content.classList.add('tab-content');

            let isFirst = true;
            for (const key in data) {
                if (data.hasOwnProperty(key) && key !== 'task_id' && key !== 'score' && key !== 'tags' && key !== 'families') {
                    const tab = document.createElement('li');
                    tab.classList.add('nav-item');

                    const link = document.createElement('a');
                    link.classList.add('nav-link');
                    link.id = `${key}-tab`;
                    link.href = `#${key}`;
                    link.setAttribute('data-toggle', 'tab');
                    link.setAttribute('role', 'tab');
                    link.textContent = key;

                    if (isFirst) {
                        link.classList.add('active');
                        isFirst = false;
                    }

                    tab.appendChild(link);
                    tabs.appendChild(tab);

                    const tabPane = document.createElement('div');
                    tabPane.classList.add('tab-pane', 'fade');
                    tabPane.id = key;
                    tabPane.setAttribute('role', 'tabpanel');
                    tabPane.setAttribute('aria-labelledby', `${key}-tab`);

                    if (key === 'screenshot') {
                        const carousel = createCarouselStructure();
                        tabPane.appendChild(carousel);
                        handleScreenshots(data[key], task_id); // Make sure this line is inside the loop
                    } else {
                        // For other tabs, use existing formatting
                        const formattedText = formatData(data[key]);
                        tabPane.innerHTML = formattedText;
                    }



                    content.appendChild(tabPane);
                }
            }

            dynamicFields.innerHTML = ''; // Clear existing fields
            dynamicFields.appendChild(tabs);
            dynamicFields.appendChild(content);
        }
        function createCarouselStructure() {
            const carouselDiv = document.createElement('div');
            carouselDiv.id = 'screenshotCarousel';
            carouselDiv.className = 'carousel slide';
            carouselDiv.setAttribute('data-ride', 'carousel');

            const carouselInner = document.createElement('div');
            carouselInner.className = 'carousel-inner';
            carouselDiv.appendChild(carouselInner);

            // Previous control
            const prevControl = document.createElement('a');
            prevControl.className = 'carousel-control-prev';
            prevControl.href = '#screenshotCarousel';
            prevControl.setAttribute('role', 'button');
            prevControl.setAttribute('data-slide', 'prev');
            prevControl.innerHTML = '<span class="carousel-control-prev-icon" aria-hidden="true"></span><span class="sr-only">Previous</span>';
            carouselDiv.appendChild(prevControl);

            // Next control
            const nextControl = document.createElement('a');
            nextControl.className = 'carousel-control-next';
            nextControl.href = '#screenshotCarousel';
            nextControl.setAttribute('role', 'button');
            nextControl.setAttribute('data-slide', 'next');
            nextControl.innerHTML = '<span class="carousel-control-next-icon" aria-hidden="true"></span><span class="sr-only">Next</span>';
            carouselDiv.appendChild(nextControl);

            return carouselDiv;
        }

        function clearDynamicFields() {
            dynamicFields.innerHTML = ''; // Clear existing fields
        }
        function formatObject(obj) {
            let result = '';
            for (const key in obj) {
                if (obj.hasOwnProperty(key)) {
                    const value = obj[key];
                    if (typeof value === 'object' && value !== null) {
                        // If the value is an object and not null, format it as a nested object
                        result += `<strong>${key}:</strong><br>${formatData(value)}<br>`;
                    } else {
                        // Treat non-object values (including strings) as simple key-value pairs
                        result += `<strong>${key}:</strong> ${value}<br>`;
                    }
                }
            }
            return result;
        }

        function formatData(data) {
            if (Array.isArray(data)) {
                // Format each element in the array
                return data.map(item => typeof item === 'object' ? formatObject(item) : item.toString()).join('<br><br>');
            } else if (typeof data === 'object' && data !== null) {
                // Format the object
                return formatObject(data);
            } else {
                // Format primitive data types (including strings)
                return data.toString();
            }
        }

        function handlePopulateCards() {
            const selectedId = analysisSelect.value;
            const selectedData = analysisData.find(item => item.id == selectedId);
            if (selectedData) {
                const task_id = selectedData.data.task_id;
                console.log("Selected Task ID:", task_id);
                generateTabs(selectedData.data, task_id); // Pass task_id as an argument
            } else {
                dynamicFields.innerHTML = '';
            }
        }

        function handleScreenshots(data, task_id) {
            if (!Array.isArray(data)) return;

            const carouselInner = document.querySelector('.carousel-inner');
            carouselInner.innerHTML = ''; // Clear existing carousel items

            data.forEach((screenshot, index) => {
                const imageUrl = `/analysis/${task_id}/screenshot/${screenshot.name}`;

                // Create a carousel item
                const item = document.createElement('div');
                item.className = 'carousel-item';
                if (index === 0) {
                    item.classList.add('active'); // Make the first item active
                }

                // Create an image element
                const img = document.createElement('img');
                img.src = imageUrl;
                img.alt = screenshot.name;
                img.style.maxWidth = '100%'; // Set max width to ensure responsiveness
                img.style.height = 'auto'; // Maintain aspect ratio

                // Append the image to the carousel item
                item.appendChild(img);

                // Append the carousel item to the carousel inner container
                carouselInner.appendChild(item);
            });
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
