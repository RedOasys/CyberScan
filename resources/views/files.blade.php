@extends('layouts.chips.main')

@section('content')
    <!-- Load jQuery first -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <!-- DataTables CSS -->
    <link href="https://cdn.datatables.net/v/bs5/dt-1.13.8/r-2.5.0/datatables.min.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/responsive/2.3.0/css/responsive.dataTables.min.css">

    <!-- FilePond CSS -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <div id="content" style="padding-right: 0px; height: 1080px; width: 1920px;">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-3 col-xxl-3" style="padding-left: 0px; margin-left: -284px;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop" >
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
                                    <input id="searchInput" class="form-control form-control-lg" type="text" placeholder="Search the files" />
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div>
                                    <h5 class="mb-0">Recent Files</h5>
                                </div>
                                <div class="ms-auto">
                                    <a class="btn btn-outline-secondary btn-sm" data-bs-toggle="modal" data-bs-target="#viewAllFilesModal" role="button" style="margin-top: 8px;">View all</a>
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
            </div>
        </div>

        <!-- File Upload Modal -->
        <div class="modal fade" id="staticBackdrop" data-bs-backdrop="true" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
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
        <div class="modal fade" id="viewAllFilesModal" tabindex="-1" aria-labelledby="viewAllFilesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="viewAllFilesModalLabel">All Files</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <table id="allFilesTable" class="table table-hover display responsive nowrap" width="100%">
                            <thead>
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">User ID</th>
                                <th scope="col">File Name</th>
                                <th scope="col">MD5 Hash</th>
                                <th scope="col">File Size (KB)</th>
                                <th scope="col">Created At</th>
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

        <style>
            #viewAllFilesModal .modal-dialog {
                max-width: 95%;
            }
            #viewAllFilesModal .modal-body {
                overflow-x: auto;
            }
            .table td {
                word-break: break-all;
            }
            .scrollable-table {
                max-height: 400px;
                overflow-y: auto;
            }
        </style>
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
        <script src="https://cdn.datatables.net/v/bs5/dt-1.13.8/r-2.5.0/datatables.min.js"></script>
        <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
        <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

        <script>
            $(document).ready(function() {
                // Initialize FilePond
                FilePond.registerPlugin(
                    FilePondPluginFileValidateType
                    // ... other plugins if necessary
                );

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
                                    // Find the last added file item and mark it with an error
                                    let lastItem = pond.getFiles().find(fileItem => fileItem.filename === jsonResponse.filename);
                                    if (lastItem) {
                                        pond.processFile(lastItem.id).then(() => {
                                            pond.removeFile(lastItem.id);
                                        });
                                    }
                                    return;
                                }
                                // Handle successful upload
                            },
                            onerror: (response) => {
                                console.error('Error during file upload.');
                            }
                        },
                    },
                    allowMultiple: true,
                    fileValidateTypeDetectType: (source, type) => new Promise((resolve, reject) => {
                        resolve(type);
                    }),
                    acceptedFileTypes: ['application/x-msdownload', 'application/zip'] // MIME type for .exe files
                });

                var allFilesTableInitialized = false;

                $('#viewAllFilesModal').on('shown.bs.modal', function () {
                    if (!allFilesTableInitialized) {
                        // Initialize DataTables for the modal table
                        $('#allFilesTable').DataTable({
                            "responsive": true,
                            "paging": true,
                            "ordering": true,
                            "info": true,
                            "searching": true
                        });
                        allFilesTableInitialized = true;
                    }

                    // Fetch and populate data for the modal table
                    fetch('/fetch-all-files')
                        .then(response => response.json())
                        .then(data => {
                            var allFilesTable = $('#allFilesTable').DataTable();
                            allFilesTable.clear();
                            data.files.forEach(file => {
                                allFilesTable.row.add([
                                    file.id,
                                    file.user_id,
                                    file.file_name,
                                    file.md5_hash,
                                    file.file_size_kb,
                                    new Date(file.created_at).toLocaleDateString(),
                                    'Actions' // Replace with actual actions
                                ]);
                            });
                            allFilesTable.draw();
                        });
                });

                $('#viewAllFilesModal').on('hidden.bs.modal', function () {
                    var allFilesTable = $('#allFilesTable').DataTable();
                    allFilesTable.clear().draw();
                });

                // Instant search for Recent Files table
                $("#searchInput").on("keyup", function() {
                    var value = $(this).val().toLowerCase();
                    $("#recentFilesTable tbody tr").filter(function() {
                        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                    });
                });
            });
        </script>
    </div>
@endsection
