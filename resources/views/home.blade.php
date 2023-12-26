@extends('layouts.chips.main')
@section('content')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">

    <script
        src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
            integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
            crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>
    <div class="d-sm-flex justify-content-between align-items-center mb-4">
        <h3 class="text-dark mb-0">Dashboard</h3>
    </div>
    <div class="row">
        <div class="row">
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow border-start-primary py-2">
                    <div class="card-body">
                        <div class="row align-items-center no-gutters">
                            <div class="col me-2">
                                <div class="text-uppercase text-primary fw-bold text-xs mb-1">
                                    <span>Uploaded Samples</span></div>
                                <div class="text-dark fw-bold h5 mb-0"><span id="uploadedSamples">Loading...</span>
                                </div>
                            </div>
                            <div class="col-auto"><i class="fas fa-upload fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            {{--            xx--}}

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow border-start-success py-2">
                    <div class="card-body">
                        <div class="row align-items-center no-gutters">
                            <div class="col me-2">
                                <div class="text-uppercase text-success fw-bold text-xs mb-1">
                                    <span>Analyzed Samples</span></div>
                                <div class="text-dark fw-bold h5 mb-0"><span id="analyzedSamples">Loading...</span>
                                </div>
                            </div>
                            <div class="col-auto"><i class="fas fa-hdd fa-2x text-gray-300"></i></div>
                        </div>
                    </div>
                </div>
            </div>

            {{--            xx--}}

            <!-- Analyzed Samples -->
            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow border-start-info py-2">
                    <div class="card-body">
                        <div class="row align-items-center no-gutters">
                            <div class="col me-2">
                                <div class="text-uppercase text-info fw-bold text-xs mb-1">
                                    <span>Detected Malware</span>
                                </div>
                                <div class="row g-0 align-items-center">
                                    <div class="col-auto">
                                        <div class="text-dark fw-bold h5 mb-0 me-3">
                                            <span id="detectedMalwareCount">Loading...</span>
                                        </div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-info" id="percentageProgressBar" aria-valuenow="0" aria-valuemin="0"
                                                 aria-valuemax="100" style="width: 0%;">
                                                <span class="visually-hidden" id="percentageDetected">0%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{--            xx--}}

            <div class="col-md-6 col-xl-3 mb-4">
                <div class="card shadow border-start-warning py-2">
                    <div class="card-body">
                        <div class="row align-items-center no-gutters">
                            <div class="col me-2">
                                <div class="text-uppercase text-warning fw-bold text-xs mb-1">
                                    <span>Queued Analysis</span></div>
                                <div class="text-dark fw-bold h5 mb-0"><span id="queuedSamples">Loading...</span></div>
                            </div>
                            <div class="col-auto"><i class="fas fa-cloud-haze2-fill fa-2x text-gray-300"></i></div>
                            <div class="col-auto"><svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor" viewBox="0 0 16 16" class="bi bi-cloud-haze2-fill fa-2x text-gray-300">
                                    <path d="M8.5 2a5.001 5.001 0 0 1 4.905 4.027A3 3 0 0 1 13 12H3.5A3.5 3.5 0 0 1 .035 9H5.5a.5.5 0 0 0 0-1H.035a3.5 3.5 0 0 1 3.871-2.977A5.001 5.001 0 0 1 8.5 2zm-6 8a.5.5 0 0 0 0 1h9a.5.5 0 0 0 0-1h-9zM0 13.5a.5.5 0 0 1 .5-.5h9a.5.5 0 0 1 0 1h-9a.5.5 0 0 1-.5-.5z"></path>
                                </svg></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{--        xx--}}

    </div>
    <div class="row">
        <div class="col-lg-7 col-xl-8 ">
            <div class="card shadow mb-4 ">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="text-primary fw-bold m-0">Task Queue</h6>
                    <div class="dropdown no-arrow">

                        <button type="button" class="btn btn-outline-primary dropdown-toggle"  >
                            <a class="dropdown-item" href="{{ route('analysis.tasks.queue') }}">Queue</a>
                        </button>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive overflow-hidden ">
                        <table class="table" id="analysisQueueTable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Target</th>
                                <th>Actions</th>
                                <th>State</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Data will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="card shadow mb-4 ">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="text-primary fw-bold m-0">Tasks Analyzed</h6>
                    <div class="dropdown no-arrow">

                        <button type="button" class="btn btn-outline-primary dropdown-toggle"  >
                            <a class="dropdown-item" href="{{ route('analysis.tasks.all') }}">Analyses</a>
                        </button>
                    </div>
                </div>

                <div class="card-body">
                    <div class="table-responsive overflow-hidden ">
                        <table class="table" id="analysisQueueFinished">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Target</th>
                                <th>Actions</th>
                                <th>State</th>
                            </tr>
                            </thead>
                            <tbody>
                            <!-- Data will be populated by JavaScript -->
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-5 col-xl-4">

                <div class="card shadow mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h6 class="text-primary fw-bold m-0">Malware Statistics</h6>
                    </div>
                    <div class="card-body">
                        <div class="chart-area">
                            <canvas id="malwareTypeChart"></canvas>
                        </div>
                        <div class="text-center small mt-4" id="legendContainer"></div>
                    </div>
                </div>

            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="text-primary fw-bold m-0">Detection Type</h6>

                </div>
                <div class="card-body">
                    <div class="chart-area">
                        <canvas
                            data-bss-chart="{&quot;type&quot;:&quot;doughnut&quot;,&quot;data&quot;:{&quot;labels&quot;:[&quot;Direct&quot;,&quot;Social&quot;,&quot;Referral&quot;],&quot;datasets&quot;:[{&quot;label&quot;:&quot;&quot;,&quot;backgroundColor&quot;:[&quot;#4e73df&quot;,&quot;#1cc88a&quot;,&quot;#36b9cc&quot;],&quot;borderColor&quot;:[&quot;#ffffff&quot;,&quot;#ffffff&quot;,&quot;#ffffff&quot;],&quot;data&quot;:[&quot;50&quot;,&quot;30&quot;,&quot;15&quot;]}]},&quot;options&quot;:{&quot;maintainAspectRatio&quot;:false,&quot;legend&quot;:{&quot;display&quot;:false,&quot;labels&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;}},&quot;title&quot;:{&quot;fontStyle&quot;:&quot;normal&quot;}}}"></canvas>
                    </div>
                    <div class="text-center small mt-4"><span class="me-2"><i class="fas fa-circle text-primary"></i>&nbsp;Static</span><span
                            class="me-2"><i class="fas fa-circle text-success"></i>&nbsp;Dynamic</span><span
                            class="me-2"><i class="fas fa-circle text-info"></i>&nbsp;AI</span></div>
                </div>
            </div>

        </div>


    </div>



    <script>
        var tableQueue;
        var tableFinished;
        // Function to initialize DataTables
        function initializeDataTables() {
            tableQueue = $('#analysisQueueTable').DataTable({
                processing: true,
                responsive: true,
                serverSide: true,
                ajax: "{{ route('analysis.tasks.queue.databrief') }}",
                columns: [
                    {data: 'analysis_id'},
                    {data: 'file_name'},
                    {data: 'actions', orderable: false, searchable: false},
                    {data: 'status'}

                ],
                drawCallback: function (settings) {
                    // For each 'analysis_id' value in the table
                    this.api().column(0).data().each(function (analysis_id) {
                        // AJAX call to update the analysis
                        $.ajax({
                            url: '/update-analysis/' + analysis_id,
                            type: 'GET',
                            success: function(response) {
                                // Handle the response
                                console.log('Analysis Updated:', response);
                                // Optionally reload the table or handle the update in another way
                            },
                            error: function(error) {
                                // Handle errors
                                console.error('Update failed:', error);
                            }
                        });
                    });
                }
            });

            tableFinished = $('#analysisQueueFinished').DataTable({
                processing: true,

                responsive: true,
                serverSide: true,
                ajax: "{{ route('analysis.tasks.queue.finishedbrief') }}",
                columns: [
                    {data: 'analysis_id'},
                    {data: 'file_name'},
                    {data: 'actions', orderable: false, searchable: false},
                    {data: 'status'}

                ],
                drawCallback: function (settings) {
                    // For each 'analysis_id' value in the table
                    this.api().column(0).data().each(function (analysis_id) {
                        // AJAX call to update the analysis
                        $.ajax({
                            url: '/update-analysis/' + analysis_id,
                            type: 'GET',
                            success: function(response) {
                                // Handle the response
                                console.log('Analysis Updated:', response);
                                // Optionally reload the table or handle the update in another way
                            },
                            error: function(error) {
                                // Handle errors
                                console.error('Update failed:', error);
                            }
                        });
                    });
                }
            });

            // Refresh DataTables every 5 seconds
            setInterval(function () {
                if (tableQueue) {
                    tableQueue.ajax.reload(null, false); // Refresh the queue table
                }
                if (tableFinished) {
                    tableFinished.ajax.reload(null, false); // Refresh the finished table
                }
            }, 5000);
        }
        function generateColorArray(numColors) {
            const colors = [];
            for (let i = 0; i < numColors; i++) {
                // Generate a random color

                const randomColor = `hsl(${Math.random() * 360}, 100%, 75%)`; // HSL: hue, saturation, lightness
                colors.push(randomColor);
            }
            return colors;
        }
        // Function to update dashboard data
        function updateDashboardData() {
            const endpoint = '/dashboard-data';
            const fetchUrl = `${window.location.origin}${endpoint}`;

            fetch(fetchUrl)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('uploadedSamples').textContent = data.uploadedSamples;
                    document.getElementById('analyzedSamples').textContent = data.analyzedSamples;
                    document.getElementById('queuedSamples').textContent = data.queuedSamples;

                    const percentage = parseFloat(data.percentageDetected).toFixed(2);
                    document.getElementById('detectedMalwareCount').textContent = `${percentage}%`;

                    const progressBar = document.getElementById('percentageProgressBar');
                    if (progressBar) {
                        progressBar.style.width = `${percentage}%`;
                        progressBar.setAttribute('aria-valuenow', percentage);
                    }
                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }

        // Function to update analyzed samples count
        function updateAnalyzedSamplesCount() {
            fetch('/analyzed-samples-count')
                .then(response => response.json())
                .then(data => {
                    const analyzedElem = document.getElementById('analyzedSamples');
                    if (analyzedElem) {
                        analyzedElem.textContent = data.analyzedSamplesCount;
                    }
                })
                .catch(error => console.error('Error fetching analyzed samples count:', error));
        }
        function updateLegend(chart) {
            const legendContainer = document.getElementById('legendContainer');
            legendContainer.innerHTML = chart.generateLegend();
        }

        // Function to fetch malware type data and initialize the chart
        function fetchMalwareTypeData() {
            fetch('/malware-stats')
                .then(response => response.json())
                .then(data => {
                    const ctx = document.getElementById('malwareTypeChart').getContext('2d');
                    const colorArray = generateColorArray(data.labels.length); // Generate a color for each label
                    const malwareChart = new Chart(ctx, {
                        type: 'doughnut',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Malware Types',
                                backgroundColor: colorArray, // Use the generated array of colors
                                borderColor: colorArray.map(color => 'rgba(255,255,255,0.5)'), // Border color can be white or any lighter shade
                                data: data.percentages,
                            }],
                        },
                        options: {
                            maintainAspectRatio: false,
                            legend: { display: false },
                            title: { display: true, text: 'Malware Types Distribution' },
                        }
                    });
                    updateLegend(malwareChart);
                })
                .catch(error => {
                    console.error('Error fetching malware type data:', error);
                });
        }

        // Function to update legend for the chart

        // DOMContentLoaded event listener
        document.addEventListener('DOMContentLoaded', function () {
            updateDashboardData();
            setInterval(updateDashboardData, 10000); // Update every 10 seconds
            updateAnalyzedSamplesCount();
            setInterval(updateAnalyzedSamplesCount, 2000); // Update every 2 seconds

            // Initialize DataTables and fetch malware type data
            initializeDataTables();
            fetchMalwareTypeData();
        });
    </script>

@endsection
