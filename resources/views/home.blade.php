@extends('layouts.chips.main')
@section('content')

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
                                <div class="text-uppercase text-info fw-bold text-xs mb-1"><span>Detected Malware</span>
                                </div>
                                <div class="row g-0 align-items-center">
                                    <div class="col-auto">
                                        <div class="text-dark fw-bold h5 mb-0 me-3"><span>ph</span></div>
                                    </div>
                                    <div class="col">
                                        <div class="progress progress-sm">
                                            <div class="progress-bar bg-info" aria-valuenow="50" aria-valuemin="0"
                                                 aria-valuemax="100" style="width: 50%;"><span class="visually-hidden">50%</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-auto">
                                <svg xmlns="http://www.w3.org/2000/svg" width="1em" height="1em" fill="currentColor"
                                     viewBox="0 0 16 16" class="bi bi-percent fa-2x text-gray-300">
                                    <path
                                        d="M13.442 2.558a.625.625 0 0 1 0 .884l-10 10a.625.625 0 1 1-.884-.884l10-10a.625.625 0 0 1 .884 0zM4.5 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5zm7 6a1.5 1.5 0 1 1 0-3 1.5 1.5 0 0 1 0 3zm0 1a2.5 2.5 0 1 0 0-5 2.5 2.5 0 0 0 0 5z"></path>
                                </svg>
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
                        <button class="btn btn-link btn-sm dropdown-toggle" aria-expanded="false"
                                data-bs-toggle="dropdown" type="button"><i class="fas fa-ellipsis-v text-gray-400"></i>
                        </button>
                        <div class="dropdown-menu shadow dropdown-menu-end animated--fade-in">
                            <p class="text-center dropdown-header">dropdown header:</p><a class="dropdown-item"
                                                                                          href="#">&nbsp;Action</a><a
                                class="dropdown-item" href="#">&nbsp;Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">&nbsp;Something else here</a>
                        </div>
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
                    <h6 class="text-primary fw-bold m-0">Analyzed Files</h6>
                    <div class="dropdown no-arrow">
                        <button class="btn btn-link btn-sm dropdown-toggle" aria-expanded="false"
                                data-bs-toggle="dropdown" type="button"><i class="fas fa-ellipsis-v text-gray-400"></i>
                        </button>
                        <div class="dropdown-menu shadow dropdown-menu-end animated--fade-in">
                            <p class="text-center dropdown-header">dropdown header:</p><a class="dropdown-item"
                                                                                          href="#">&nbsp;Action</a><a
                                class="dropdown-item" href="#">&nbsp;Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">&nbsp;Something else here</a>
                        </div>
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
                    <h6 class="text-primary fw-bold m-0">Detection Type</h6>
                    <div class="dropdown no-arrow">
                        <button class="btn btn-link btn-sm dropdown-toggle" aria-expanded="false"
                                data-bs-toggle="dropdown" type="button"><i class="fas fa-ellipsis-v text-gray-400"></i>
                        </button>
                        <div class="dropdown-menu shadow dropdown-menu-end animated--fade-in">
                            <p class="text-center dropdown-header">dropdown header:</p><a class="dropdown-item"
                                                                                          href="#">&nbsp;Action</a><a
                                class="dropdown-item" href="#">&nbsp;Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">&nbsp;Something else here</a>
                        </div>
                    </div>
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
        <div class="col-lg-7 col-xl-8 ">


        </div>
    </div>
    <div class="row">
        <div class="col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="text-primary fw-bold m-0">Malware Statistics</h6>
                </div>
                <div class="card-body">
                    <h4 class="small fw-bold">ph<span class="float-end">20%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-danger" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100"
                             style="width: 20%;"><span class="visually-hidden">20%</span></div>
                    </div>
                    <h4 class="small fw-bold">ph<span class="float-end">40%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-warning" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100"
                             style="width: 40%;"><span class="visually-hidden">40%</span></div>
                    </div>
                    <h4 class="small fw-bold">ph<span class="float-end">60%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-primary" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                             style="width: 60%;"><span class="visually-hidden">60%</span></div>
                    </div>
                    <h4 class="small fw-bold">ph<span class="float-end">80%</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-info" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100"
                             style="width: 80%;"><span class="visually-hidden">80%</span></div>
                    </div>
                    <h4 class="small fw-bold">ph<span class="float-end">Complete!</span></h4>
                    <div class="progress mb-4">
                        <div class="progress-bar bg-success" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"
                             style="width: 100%;"><span class="visually-hidden">100%</span></div>
                    </div>
                </div>
            </div>
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="text-primary fw-bold m-0">Analysis Queue</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-5 col-xl-4 col-xxl-6">
            <div class="card shadow mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h6 class="text-primary fw-bold m-0">Detection Type</h6>

                    <div class="dropdown no-arrow">
                        <button class="btn btn-link btn-sm dropdown-toggle" aria-expanded="false"
                                data-bs-toggle="dropdown" type="button"><i class="fas fa-ellipsis-v text-gray-400"></i>
                        </button>
                        <div class="dropdown-menu shadow dropdown-menu-end animated--fade-in">
                            <p class="text-center dropdown-header">dropdown header:</p><a class="dropdown-item"
                                                                                          href="#">&nbsp;Action</a><a
                                class="dropdown-item" href="#">&nbsp;Another action</a>
                            <div class="dropdown-divider"></div>
                            <a class="dropdown-item" href="#">&nbsp;Something else here</a>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table" id="analysisQueueTable">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Target</th>
                                <th>Created On</th>
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
    </div>

    <link href="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.css" rel="stylesheet">

    <script
        src="https://code.jquery.com/jquery-3.7.1.js"
        integrity="sha256-eKhayi8LEQwp4NKxN+CfCh+3qOVUtJn3QNZ0TciWLP4="
        crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"
            integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN"
            crossorigin="anonymous"></script>

    <script src="https://cdn.datatables.net/v/dt/dt-1.13.8/datatables.min.js"></script>
    <script>
        function updateDashboardData() {
            const endpoint = '/dashboard-data';
            const fetchUrl = `${window.location.origin}${endpoint}`;
            fetch(fetchUrl)

                .then(response => response.json())
                .then(data => {
                    const uploadedElem = document.getElementById('uploadedSamples');
                    const analyzedElem = document.getElementById('analyzedSamples');
                    const queuedElem = document.getElementById('queuedSamples');
                    if (uploadedElem && analyzedElem && queuedElem) {
                        uploadedElem.textContent = data.uploadedSamples;
                        analyzedElem.textContent = data.analyzedSamples;
                        queuedElem.textContent = data.queuedSamples;
                    }


                })
                .catch(error => {
                    console.error('Error fetching data:', error);
                });
        }

        document.addEventListener('DOMContentLoaded', function () {
            updateDashboardData();
            setInterval(updateDashboardData, 10000); // Update every 10 seconds

            // Function to fetch queue data from the Cuckoo API and initialize DataTable
            $(document).ready(function () {
                var table = $('#analysisQueueTable').DataTable({
                    processing: true,
                    responsive: true,
                    serverSide: true,
                    ajax: "{{ route('analysis.tasks.queue.data') }}",
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

                // Refresh DataTable every 5 seconds
                setInterval(function () {
                    table.ajax.reload(null, false); // false means don't reset user paging
                }, 5000); // 5000 milliseconds = 5 seconds
            });

            $(document).ready(function () {
                var table = $('#analysisQueueFinished').DataTable({
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

                // Refresh DataTable every 5 seconds
                setInterval(function () {
                    table.ajax.reload(null, false); // false means don't reset user paging
                }, 5000); // 5000 milliseconds = 5 seconds
            });

        });
    </script>

@endsection
