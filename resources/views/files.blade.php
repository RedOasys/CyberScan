@extends('layouts.chips.main')

@section('content')
    <!-- Load jQuery first -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/r-2.5.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css">

    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <div class="row">
        <div class="col-12 col-lg-3 col-xxl-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-grid">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                data-bs-target="#staticBackdrop">
                            + Upload
                        </button>
                    </div>
                    <div class="fm-menu">
                        <div class="list-group list-group-flush"></div>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <h5 class="text-primary mb-0 font-weight-bold">{{ $totalSizeGB }} MB</h5>
                    <p class="mb-0 mt-2"><span class="text-secondary">Used</span></p>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-9">
            <div class="card">
                <div class="card-body">
                    <div class="fm-search">
                        <div class="mb-0">
                            <input id="searchInput" class="form-control form-control-lg" type="text"
                                   placeholder="Search the files"/>
                        </div>
                    </div>
                    <div class="d-flex align-items-center">
                        <div>
                            <h5 class="mb-0">Recent Files</h5>
                        </div>
                        <div class="ms-auto">
                            <a class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal"
                               data-bs-target="#viewAllFilesModal" role="button" style="margin-top: 8px;">View all</a>
                        </div>
                    </div>
                    <div class="table-responsive mt-3">
                        <table class="table table-striped table-hover table-sm mb-0" id="recentFilesTable">
                            <thead>
                            <tr>
                                <th>Filename</th>
                                <th>Uploader</th>
                                <th>Uploaded</th>
                                <th></th>
                            </tr>
                            </thead>

                            <tbody>
                            @foreach ($recentFiles as $file)
                                <tr data-filename="{{ $file->file_name }}">
                                    <td>{{ $file->file_name }}</td>
                                    <td>{{ $file->user ? $file->user->name : 'Unknown' }}</td>
                                    <td>{{ $file->updated_at->diffForHumans() }}</td>
                                    <td><!-- Additional actions if needed --></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>


        <!-- File Upload Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="true" tabindex="-1"
             aria-labelledby="staticBackdropLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="fileUploadModalLabel">Upload File</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="file" class="filepond" name="file" multiple>
                    </div>
                </div>
            </div>
        </div>

        <!-- View All Files Modal -->

        <!-- View All Files Modal -->
        <div class="modal fade flex-xxl-wrap" id="viewAllFilesModal" tabindex="-1"
             aria-labelledby="viewAllFilesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewAllFilesModalLabel">All Files</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <table id="allFilesTable" class="table table-hover display responsive nowrap">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>

                                <th scope="col">File Name</th>
                                <th scope="col">MD5 Hash</th>
                                <th scope="col">File Size (KB)</th>

                                <th scope="col">Actions</th>
                            </tr>
                            </thead>
                            <tbody id="allFilesTableBody">
                            <!-- Table rows will be inserted here by JavaScript -->
                            </tbody>
                        </table>


                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="viewAllFilesModal" tabindex="-1" aria-labelledby="viewAllFilesModalLabel"
             aria-hidden="true">
            <div class="modal-dialog modal-xl modal-dialog-centered">
                <div class="modal-content">
                    <div class="container-fluid">
                        <!-- Modal Header -->
                        <div class="modal-header">
                            <h5 class="modal-title" id="viewAllFilesModalLabel">All Files</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <!-- Modal Body -->
                        <div class="modal-body">
                            <table id="allFilesTable" class="table table-hover display responsive nowrap">
                                <!-- Table rows will be inserted here by JavaScript -->
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <style>
            #viewAllFilesModal .modal-dialog {
                max-width: 95%;
            }

            #viewAllFilesModal .modal-body {
                overflow-x: auto;

                overflow-y: auto;
            }

            .table td {
                word-break: break-all;
            }

        </style>



        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/r-2.5.0/datatables.min.js"></script>
        <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
        <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
        <script>
            $(document).ready(function () {
                // Initialize FilePond with necessary plugins
                FilePond.registerPlugin(FilePondPluginFileValidateType);
                const inputElement = document.querySelector('input[type="file"]');
                const pond = FilePond.create(inputElement, {
                    server: {
                        url: '/upload',
                        process: {
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            },
                            ondata: (formData) => {
                                formData.append('filename', inputElement.value);
                                return formData;
                            },
                            onload: (response) => {
                                const jsonResponse = JSON.parse(response);
                                if (jsonResponse.message === 'File already exists') {
                                    let lastItem = pond.getFiles().find(fileItem => fileItem.filename === jsonResponse.filename);
                                    if (lastItem) {
                                        pond.processFile(lastItem.id).then(() => {
                                            pond.removeFile(lastItem.id);
                                        });
                                    }
                                    return;
                                }
                                // Handle successful upload here
                            },
                            onerror: (response) => {
                                console.error('Error during file upload: ' + response);
                            }
                        },
                    },
                    allowMultiple: true,
                    fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
                        resolve(type);
                    }),

                });

                // DataTables initialization for the 'View All Files' modal
                var allFilesTableInitialized = false;
                $('#viewAllFilesModal').on('shown.bs.modal', function () {
                    if (!allFilesTableInitialized) {
                        var allFilesTable = $('#allFilesTable').DataTable({
                            processing: true,
                            responsive: true,
                            serverSide: true,
                            ajax: "{{ route('fetchAllFiles') }}",
                            columns: [
                                {data: "file_id"},

                                {data: "file_name"},
                                {data: "md5_hash"},
                                {data: "file_size_kb"},

                                {data: "actions", orderable: false, searchable: false}
                            ],
                            createdRow: function (row, data, dataIndex) {
                                // Check if an analysis exists for the file
                                $.getJSON('/has-analysis/' + data.file_id, function (response) {
                                    if (response.hasAnalysis) {
                                        // Create a dropdown menu for actions
                                        var actionsHtml = '<div class="dropdown">' +
                                            '<button class="btn btn-secondary dropdown-toggle" type="button" id="dropdownMenuButton" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">' +
                                            'Actions' +
                                            '</button>' +
                                            '<div class="dropdown-menu" aria-labelledby="dropdownMenuButton">' +
                                            '<a class="dropdown-item" href="/edit/' + data.file_id + '">Edit</a>' +
                                            '<a class="dropdown-item" href="/delete/' + data.file_id + '">Delete</a>' +
                                            '</div>' +
                                            '</div>';
                                        $('td:eq(6)', row).html(actionsHtml);
                                    }
                                });
                            }
                        });
                        allFilesTableInitialized = true;
                    } else {
                        $('#allFilesTable').DataTable().ajax.reload();
                    }
                });

                $('#viewAllFilesModal').on('hidden.bs.modal', function () {
                    var allFilesTable = $('#allFilesTable').DataTable();
                    allFilesTable.clear().draw();
                });

                // Instant search for the Recent Files table
                $("#searchInput").on("keyup", function () {
                    var value = $(this).val().toLowerCase();
                    $("#recentFilesTable tbody tr").filter(function () {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
            });
        </script>


    </div>
@endsection
