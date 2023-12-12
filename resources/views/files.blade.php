@extends('layouts.chips.main')

@section('content')
    <div id="content" style="padding-right: 0px; height: 1080px; width: 1920px;">
        <div class="container">
            <div class="row">
                <div class="col-12 col-lg-3 col-xxl-3" style="padding-left: 0px; margin-left: -284px;">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-grid">
                                <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
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
                                    <form action="{{ route('searchFiles') }}" method="GET">
                                        <div class="input-group input-group-lg">
                                            <span class="bg-transparent input-group-text input-group-text"><i class="fa fa-search"></i></span>
                                            <input id="searchInput" class="form-control form-control" type="text" name="search" placeholder="Search the files" />
                                            <button type="submit" class="btn btn-primary">Search</button>
                                        </div>
                                    </form>
                                </div>
                            </div>
                            <div class="d-flex align-items-center">
                                <div>
                                    <h5 class="mb-0">Recent Files</h5>
                                </div>
                                <div class="ms-auto">
                                    <a class="btn btn-outline-secondary btn-sm" role="button" style="margin-top: 8px;">View all</a>
                                </div>
                            </div>
                            <div class="table-responsive mt-3">
                                <table class="table table-striped table-hover table-sm mb-0">
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

    <!-- FilePond CSS and JavaScript -->
    <link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
    <script src="https://unpkg.com/filepond/dist/filepond.min.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
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
                acceptedFileTypes: ['application/x-msdownload'] // MIME type for .exe files
            });

            // Instant search functionality
            const searchInput = document.getElementById('searchInput');
            const tableRows = document.querySelectorAll('tbody tr');

            searchInput.addEventListener('input', function() {
                const searchTerm = this.value.trim().toLowerCase();

                tableRows.forEach(function(row) {
                    const filename = row.querySelector('td:first-child').textContent.toLowerCase();
                    if (filename.includes(searchTerm)) {
                        row.style.display = '';
                    } else {
                        row.style.display = 'none';
                    }
                });
            });
        });
    </script>

@endsection
